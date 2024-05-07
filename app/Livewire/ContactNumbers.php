<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use App\Models\Post;

class ContactNumbers extends Component
{
    public $mainContactNumber;
    public $mainWhatsappNumber;
    public $data;
    public $type;
    public $shop;

    public bool $showAllContactNumbers = false;

    public bool $showAllWhatsappNumbers = false;

    public function render()
    {
        return view('livewire.contact-numbers');
    }

    public function mount($dataId, string $type)
    {

        $this->type = $type;

        if ($this->type == "shop") {

            $this->data = User::find($dataId);
        }

        if ($this->type == "post") {

            $this->data = Post::find($dataId);
        }

        $phone = $this->data->phone;

        $whatsapp_number = $this->data->whatsapp_number;

        $this->mainContactNumber = str_replace(substr($phone, 4, 5), $this->numberConverter($phone), $phone);

        $this->mainWhatsappNumber = str_replace(substr($whatsapp_number, 4, 5), $this->numberConverter($whatsapp_number), $whatsapp_number);

    }

    public function numberConverter($phone)
    {

        $times = strlen(trim(substr($phone, 4, 5)));

        $star = '';

        for ($i = 0; $i < $times; $i++) {

            $star .= 'X';
        }

        return $star;
    }

    public function showAllContactNumbersHandler()
    {
        $this->showAllContactNumbers = true;
    }

    public function showAllWhatsappNumbersHandler()
    {
        $this->showAllWhatsappNumbers = true;
    }
}
