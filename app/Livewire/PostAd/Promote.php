<?php

namespace App\Livewire\PostAd;

use App\Models\Package;
use App\Models\Post;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Component;
use \App\Helpers\UrlGen;

class Promote extends Component
{
    public $post;

    public $packages;
    public $topAds;
    public $bumpAds;
    public $giveawayPackages = [];

    public array $formPackages;

    public Package $selectedPackage;

    public Package $selectedGiveawayPackage;

    public $totallyAllowedPackagesCount = 0;

    public $totallyUsedPackageCount = 0;

    public $error_output;

    public function render()
    {
        return view('livewire.post-ad.promote');
    }

    public function mount(int $postId)
    {
        $this->post = Post::with(['postValues' => function ($query) {

            $query->whereNotNull('value');

            $query->with(['field' => function ($query) {

                $query->where('is_search_item_visible', 1);

                $query->whereHas('unit', function ($query) { });

            }]);

        }])->find($postId);

        $loadPackages = Package::where("active", 1);

        $this->packages = $loadPackages->get();

        $this->topAds = $loadPackages->where("packge_type", "Top ads")->get();

        $this->bumpAds = $loadPackages->where("packge_type", "Bump Ads")->get();

        if(auth()->user() && auth()->user()->membership && auth()->user()->membership->member()->exists()){
            $this->giveawayPackages = $this->getGiveawayPackages();
                
            $this->totallyUsedPackageCount = $this->getTotallyUsedPackageCount(auth()->user()->id);
            
            $this->totallyAllowedPackagesCount = $this->getTotallyAllowedPackagesCount($this->giveawayPackages);
        }
    }

    protected function getGiveawayPackages(){
        return json_decode(auth()->user()->membership->giveaway_packages, true);
    }

    protected function getTotallyUsedPackageCount($user_id){
        return Transaction::valid('ad-promotion')->where('user_id', $user_id)->giveawayTransactions()->count();
    }

    protected function getTotallyAllowedPackagesCount($giveawayPackages){
        return array_sum(array_map(function($package) {
            return $package['count'];
        }, array_filter($giveawayPackages, function($package) {
            return !is_null($package['count']);
        })));
    }

    public function selectPackage(Package $package)
    {
        $this->selectedPackage = $package;
    }

    public function selectGiveawayMembershipPackage(Package $package)
    {
        $this->selectedGiveawayPackage = $package;
    }

    public function save()
    {
        if(isset($this->selectedPackage)){

            if ($this->selectedPackage->packge_type == "Free Ads" || $this->selectedPackage->price == "0.00") {

                return redirect(\App\Helpers\UrlGen::post($this->post));

            } else {

                return redirect()->route('memberships.checkout', [
                    'order_type' => 'ad-promotions',
                    'order_id' => $this->selectedPackage->id,
                    'post_id' => $this->post->id
                ]);
            }

        }else if(isset($this->selectedGiveawayPackage) && $this->getTotallyAllowedPackagesCount(getGiveawayPackages()) > $this->getTotallyUsedPackageCount(auth()->user()->id)){

            return $this->promoteUsingGiveaway($this->selectedGiveawayPackage);

        }else if(isset($this->selectedGiveawayPackage) && $this->getTotallyAllowedPackagesCount(getGiveawayPackages()) <= $this->getTotallyUsedPackageCount(auth()->user()->id)){

            return $this->error_output = "You have already used all of your giveaway ad promotions.";

        }else{

            return $this->error_output = "Please select an Ad promotion.";
        }

    }

    protected function promoteUsingGiveaway(Package $package)
    {

        if (!auth()->check()) {
            return $this->error_output = "You are not authenticated to obtain giveaway ad promotions.";
        }

        if (auth()->user()->membership->member()->doesntExist()) {
            return $this->error_output = "You dont havy any memberships to obtain giveaway ad promotions.";
        }

        if(Transaction::valid('ad-promotion')->where('post_id', $this->post->id)->exists()){
            
            $status = false;
            $message = '`'.$this->post->title.'` has allready been successfully promoted to a '.$package->name.'!';

        }else{

            try {

                $transaction_id = "giveaway_package";
                
                $transaction = new Transaction;

                $user = auth()->user();

                $transaction->user()->associate($user);

                $transaction->name = auth()->user()->name;

                $transaction->email = auth()->user()->email;

                $transaction->phone = auth()->user()->phone;

                $transaction->payment_type = "ad-promotion";

                $transaction->post()->associate($this->post);

                $transaction->package()->associate($package);

                $transaction->currency()->associate($package->currency);

                $transaction->reference_id = generateReferenceNumber(5);

                $transaction->payment_started_datetime = Carbon::now();

                $transaction->ipg_transaction_id = $transaction_id;

                $transaction->gross_amount = $package->price;

                $transaction->discount = $package->price;

                $transaction->handling_fee = 0;

                $transaction->net_amount = 0.0;

                $payment_started_datetime = Carbon::now();

                if($transaction->payment_type == "membership"){

                    $nextPaymentMonth = $payment_started_datetime->settings(['monthOverflow' => false])->addMonths(config('subscriptions.memberships.adding_months'));

                    $nextPaymentDay = Carbon::parse($nextPaymentMonth)->settings(['monthOverflow' => false])->addDays(config('subscriptions.memberships.payment_pending_time'));

                }

                if($transaction->payment_type == "ad-promotion"){

                    $nextPaymentMonth = $payment_started_datetime->settings(['monthOverflow' => false])->addDays($package->promo_duration); // Refer `packages` table.

                    $nextPaymentDay = Carbon::parse($nextPaymentMonth)->settings(['monthOverflow' => false])->addDays(config('subscriptions.ad-promotions.payment_pending_time'));

                }

                $transaction->payment_status = "success";

                $transaction->response_transaction_id = $transaction_id;

                $transaction->payment_method = $transaction_id;

                $transaction->active = 1;

                $transaction->payment_valid_untill_datetime = $nextPaymentMonth;

                $transaction->payment_due_datetime = $nextPaymentDay;

                $transaction->save();

                $message = '`'.$this->post->title.'` has been successfully promoted to a '.$package->name.'!';

                return redirect(UrlGen::post($this->post))->with('giveaway_package_purchase', ['status' => true, 'message' => $message]);

            } catch (\Throwable $th) {
                
                $message = "Unexpected error occured. Please try again. If not worked please contact us.";

                return redirect(UrlGen::post($this->post))->with('giveaway_package_purchase', ['status' => false, 'message' => $message]);
            }

        }
    }
}
