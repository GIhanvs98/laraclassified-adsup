<?php

namespace App\Http\Controllers\Web\Public;

use Illuminate\Http\Request;
use App\Models\Category;
use Larapen\LaravelMetaTags\Facades\MetaTag;
use App\Models\CategoryGroup;
use App\Models\SubCategoryGroup;
use App\Models\City;
use App\Models\Post;
use Illuminate\Support\Facades\DB;

class AdController extends FrontController
{

    public function postAd(){
		
		// Meta Tags
		[$title, $description, $keywords] = getMetaTag('create');
		MetaTag::set('title', $title);
		MetaTag::set('description', strip_tags($description));
		MetaTag::set('keywords', $keywords);

        // If user have allready set up a draft post. store the data and the link also.

        $categoryGroups = CategoryGroup::with('subCategoryGroups')->get();

        return view('post.post-ad.index', ['categoryGroups' => $categoryGroups]);
    }

    public function postAdMainCategory(Request $request, SubCategoryGroup $mainCategory){
        
		// Meta Tags
		[$title, $description, $keywords] = getMetaTag('create');
		MetaTag::set('title', $title);
		MetaTag::set('description', strip_tags($description));
		MetaTag::set('keywords', $keywords);

        if($mainCategory->categories->count() == 1){

            if($mainCategory->categories->first()->children->count() == 0){

                return redirect()->route('post-ad.location', ['mainCategory' => $mainCategory->id, 'category' => $mainCategory->categories->first()->id ]);

            }else if($mainCategory->categories->first()->children->count() == 1){

                return redirect()->route('post-ad.location', ['mainCategory' => $mainCategory->id, 'category' => $mainCategory->categories->first()->children->first()->id ]);

            }else{

                return view('post.post-ad.category', ['mainCategoryId' => $mainCategory->id]);
            }

        }else{

            return view('post.post-ad.category', ['mainCategoryId' => $mainCategory->id]);
        }
    }

    public function postAdLocation(Request $request, SubCategoryGroup $mainCategory, Category $category){

		// Meta Tags
		[$title, $description, $keywords] = getMetaTag('create');
		MetaTag::set('title', $title);
		MetaTag::set('description', strip_tags($description));
		MetaTag::set('keywords', $keywords);

        // Check if the 'l' parameter exists and has the value 'clear'
        if ($request->has('l') && $request->input('l') == 'clear') {

            // Remove city id so that it can be set.
            $request->session()->forget('post-ad.cityId');
        }

        // Redirection if the city id allready exists.
        if ($request->session()->has('post-ad.cityId') && City::whereId(session('post-ad.cityId'))->where('active', 1)->exists()) {

            return redirect()->route('post-ad.details', ['mainCategory' => $mainCategory->id, 'category' => $category->id, 'location' => session('post-ad.cityId')]);

        }else{
            
            return view('post.post-ad.location', ['mainCategoryId' => $mainCategory->id, 'categoryId' => $category->id]);
        }

    }

    public function postAdDetails(Request $request, SubCategoryGroup $mainCategory, Category $category, City $city ){

		// Meta Tags
		[$title, $description, $keywords] = getMetaTag('create');
		MetaTag::set('title', $title);
		MetaTag::set('description', strip_tags($description));
		MetaTag::set('keywords', $keywords);

        return view('post.post-ad.details', ['mainCategoryId' => $mainCategory->id, 'categoryId' => $category->id, 'cityId' => $city->id]);
    }

    public function postAdPromote(string $postId, Request $request){

		// Meta Tags
		[$title, $description, $keywords] = getMetaTag('create');
		MetaTag::set('title', $title);
		MetaTag::set('description', strip_tags($description));
		MetaTag::set('keywords', $keywords);

        $post = DB::table('posts')->where('id', $postId);

        if ($post->doesntExist()) {

            return abort(404);
        }

        $getPost = Post::where('id', $postId);

        if ($getPost->doesntExist()) {

            return view('pages.order-summary-error', [
                'section' => 'user-unverified',
            ]);
        }

        $post = $getPost->first();

        return view('post.post-ad.promote', ['postId' => $postId, 'post' => $post]);
    }


    // ------------------------ Edit section ------------------------

    public function postAdEdit(Post $post, Request $request)
    {
        if (isset(auth()->user()->is_admin) && auth()->user()->is_admin == "1") {

            $userId = $post->user_id;

            if ($post->user()->whereId($userId)->doesntExist()) {
                // Unauthrozed.
                return abort(401);
            }

        } else if (auth()->check()) {

            $userId = auth()->user()->id;

            if ($post->user()->whereId($userId)->doesntExist()) {
                // Unauthrozed.
                return abort(401);
            }

        } else if (request()->session()->has('guest-user')) {

            $guestUser = session('guest-user');

            if($guestUser->doesntHavePost($post->id)){
                // Unauthrozed.
                return abort(401);
            }

        } else {
            // Unauthrozed.
            return abort(401);
        }

        MetaTag::set('title', $post->title . " | " . config('app.name'));
        MetaTag::set('description', strip_tags($post->description));

        return view('post.post-ad.edit.details', ['postId' => $post->id]);
    }

}