<x-mail::message>
# Your Ad Reviews
 
Hello {{ $name }},

Your ad "{{ $title }}", posted on {{ $homePage }}, does not follow our guidelines, thus has not been approved.

## The reason why we could not approve your ad
 
<x-mail::panel>
{{ $reason }}
</x-mail::panel>
 
## What you should do next

To edit your ad, please click the following link and login to your account.

<x-mail::button :url="$adEditPage">
Edit Ad
</x-mail::button>

Use this link if the button didn't worked: {{ $adEditPage }}

If you have purchased a promotion for this ad, it will be applied as soon as your ad has been edited and approved.

For further clarifications, feel free to reply to this email and one of our agents will assist you.

Regards,<br>
The support team at {{ $appName }}
</x-mail::message>