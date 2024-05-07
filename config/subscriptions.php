<?php

return [

    // Later for higher customizability. Future developers can take the below data to be enter to the db by admin directly.

    "payment_renewal_date" => 1, # Format:dates - On the 1st of every moth is the default. But it can be vary depending on the payment date of the customer.
    
    
    /*-------------------------------
    / For memberships
    /-------------------------------*/
    
    "memberships" => [
            
        "payment_pending_time" => 10, #  Format:dates - This is the amount of dates given to complete the renewal payment.

        "default_id" => 1,

        "adding_months" => 1, # This means number of months per the subscriptions should be repurchased.

        "precision" => false, # to show hours:minutes:seconds or not. Only used for the `custom date calculator` and not for `Carbon`
    
    ],
        

    /*-------------------------------
    / For ad-promotions.
    /-------------------------------*/

    "ad-promotions" => [
            
        // For ad-promotions there is no extra days for payment to be settled. Since it's a one time payment.

        "payment_pending_time" => 0, # Format:dates - This is the amount of dates given to complete the renewal ad-promotion.

        "default_id" => 4,

        // "adding_months" => table:Packages -> column:promo_duration
    
        "precision" => false, # To show hours:minutes:seconds or not. Only used for the `custom date calculator` and not for `Carbon`
    
    ],

    'ad_reviewing_duration' => 7, # In days

];