<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'The :attribute must be accepted.',
    'accepted_if' => 'The :attribute must be accepted when :other is :value.',
    'active_url' => 'The :attribute is not a valid URL.',
    'after' => 'The :attribute must be a date after :date.',
    'after_or_equal' => 'The :attribute must be a date after or equal to :date.',
    'alpha' => 'The :attribute must only contain letters.',
    'alpha_dash' => 'The :attribute must only contain letters, numbers, dashes and underscores.',
    'alpha_num' => 'The :attribute must only contain letters and numbers.',
    'array' => 'The :attribute must be an array.',
    'before' => 'The :attribute must be a date before :date.',
    'before_or_equal' => 'The :attribute must be a date before or equal to :date.',
    'between' => [
        'numeric' => 'The :attribute must be between :min and :max.',
        'file' => 'The :attribute must be between :min and :max kilobytes.',
        'string' => 'The :attribute must be between :min and :max characters.',
        'array' => 'The :attribute must have between :min and :max items.',
    ],
    'boolean' => 'The :attribute field must be true or false.',
    'confirmed' => 'The :attribute confirmation does not match.',
    'current_password' => 'The password is incorrect.',
    'date' => 'The :attribute is not a valid date.',
    'date_equals' => 'The :attribute must be a date equal to :date.',
    'date_format' => 'The :attribute does not match the format :format.',
    'declined' => 'The :attribute must be declined.',
    'declined_if' => 'The :attribute must be declined when :other is :value.',
    'different' => 'The :attribute and :other must be different.',
    'digits' => 'The :attribute must be :digits digits.',
    'digits_between' => 'The :attribute must be between :min and :max digits.',
    'dimensions' => 'The :attribute has invalid image dimensions.',
    'distinct' => 'The :attribute field has a duplicate value.',
    'email' => 'The :attribute must be a valid email address.',
    'ends_with' => 'The :attribute must end with one of the following: :values.',
    'enum' => 'The selected :attribute is invalid.',
    'exists' => 'The selected :attribute is invalid.',
    'file' => 'The :attribute must be a file.',
    'filled' => 'The :attribute field must have a value.',
    'gt' => [
        'numeric' => 'The :attribute must be greater than :value.',
        'file' => 'The :attribute must be greater than :value kilobytes.',
        'string' => 'The :attribute must be greater than :value characters.',
        'array' => 'The :attribute must have more than :value items.',
    ],
    'gte' => [
        'numeric' => 'The :attribute must be greater than or equal to :value.',
        'file' => 'The :attribute must be greater than or equal to :value kilobytes.',
        'string' => 'The :attribute must be greater than or equal to :value characters.',
        'array' => 'The :attribute must have :value items or more.',
    ],
    'image' => 'The :attribute must be an image.',
    'in' => 'The selected :attribute is invalid.',
    'in_array' => 'The :attribute field does not exist in :other.',
    'integer' => 'The :attribute must be an integer.',
    'ip' => 'The :attribute must be a valid IP address.',
    'ipv4' => 'The :attribute must be a valid IPv4 address.',
    'ipv6' => 'The :attribute must be a valid IPv6 address.',
    'json' => 'The :attribute must be a valid JSON string.',
    'lt' => [
        'numeric' => 'The :attribute must be less than :value.',
        'file' => 'The :attribute must be less than :value kilobytes.',
        'string' => 'The :attribute must be less than :value characters.',
        'array' => 'The :attribute must have less than :value items.',
    ],
    'lte' => [
        'numeric' => 'The :attribute must be less than or equal to :value.',
        'file' => 'The :attribute must be less than or equal to :value kilobytes.',
        'string' => 'The :attribute must be less than or equal to :value characters.',
        'array' => 'The :attribute must not have more than :value items.',
    ],
    'mac_address' => 'The :attribute must be a valid MAC address.',
    'max' => [
        'numeric' => 'The :attribute must not be greater than :max.',
        'file' => 'The :attribute must not be greater than :max kilobytes.',
        'string' => 'The :attribute must not be greater than :max characters.',
        'array' => 'The :attribute must not have more than :max items.',
    ],
    'mimes' => 'The :attribute must be a file of type: :values.',
    'mimetypes' => 'The :attribute must be a file of type: :values.',
    'min' => [
        'numeric' => 'The :attribute must be at least :min.',
        'file' => 'The :attribute must be at least :min kilobytes.',
        'string' => 'The :attribute must be at least :min characters.',
        'array' => 'The :attribute must have at least :min items.',
    ],
    'multiple_of' => 'The :attribute must be a multiple of :value.',
    'not_in' => 'The selected :attribute is invalid.',
    'not_regex' => 'The :attribute format is invalid.',
    'numeric' => 'The :attribute must be a number.',
    'password' => 'The password is incorrect.',
    'present' => 'The :attribute field must be present.',
    'prohibited' => 'The :attribute field is prohibited.',
    'prohibited_if' => 'The :attribute field is prohibited when :other is :value.',
    'prohibited_unless' => 'The :attribute field is prohibited unless :other is in :values.',
    'prohibits' => 'The :attribute field prohibits :other from being present.',
    'regex' => 'The :attribute format is invalid.',
    'required' => 'The :attribute field is required.',
    'required_array_keys' => 'The :attribute field must contain entries for: :values.',
    'required_if' => 'The :attribute field is required when :other is :value.',
    'required_unless' => 'The :attribute field is required unless :other is in :values.',
    'required_with' => 'The :attribute field is required when :values is present.',
    'required_with_all' => 'The :attribute field is required when :values are present.',
    'required_without' => 'The :attribute field is required when :values is not present.',
    'required_without_all' => 'The :attribute field is required when none of :values are present.',
    'same' => 'The :attribute and :other must match.',
    'size' => [
        'numeric' => 'The :attribute must be :size.',
        'file' => 'The :attribute must be :size kilobytes.',
        'string' => 'The :attribute must be :size characters.',
        'array' => 'The :attribute must contain :size items.',
    ],
    'starts_with' => 'The :attribute must start with one of the following: :values.',
    'string' => 'The :attribute must be a string.',
    'timezone' => 'The :attribute must be a valid timezone.',
    'unique' => 'The :attribute has already been taken.',
    'uploaded' => 'The :attribute failed to upload.',
    'url' => 'The :attribute must be a valid URL.',
    'uuid' => 'The :attribute must be a valid UUID.',
    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'email' => [
            'required' => 'ઈમેલ ફીલ્ડ આવશ્યક છે.',
        ],
        'password' => [
            'required' => 'પાસવર્ડ ફીલ્ડ આવશ્યક છે.',
        ],
        'fullname' => [
            'required' => 'નામ ફીલ્ડ આવશ્યક છે.',
        ],
        'username' => [
            'required' => 'વપરાશકર્તા નામ ફીલ્ડ આવશ્યક છે.',
        ],
        'profile_img' => [
            'max' =>  'છબીનું કદ :max kilobytes કરતાં મોટું ન હોવું જોઈએ.',
            'required' => 'છબી ફીલ્ડ આવશ્યક છે.',
        ],
        'mobile_number' => [
            'required' => 'મોબાઇલ નંબર ફીલ્ડ આવશ્યક છે.',
        ],
        'image' => [
            'max' =>  'છબીનું કદ :max kilobytes કરતાં મોટું ન હોવું જોઈએ.',
            'required' => 'છબી ફીલ્ડ આવશ્યક છે.',
        ],
        'level_name' => [
            'required' => 'નામ ફીલ્ડ આવશ્યક છે.',
        ],
        'level_order' => [
            'required' => 'લેવલ ઓર્ડર નંબર ફીલ્ડ આવશ્યક છે.',
        ],
        'level_id' => [
            'required' => 'લેવલ ફીલ્ડ જરૂરી છે.',
        ],
        'name' => [
            'required' => 'નામ ફીલ્ડ આવશ્યક છે.',
        ],
        'start_date' => [
            'required' => 'પ્રારંભ તારીખ ફીલ્ડ આવશ્યક છે.',
        ],
        'end_date' => [
            'required' => 'સમાપ્તિ તારીખ ફીલ્ડ આવશ્યક છે.',
        ],
        'price' => [
            'required' => 'એન્ટ્રી ફી ફીલ્ડ આવશ્યક છે.',
        ],
        'no_of_user' => [
            'required' => 'વપરાશકર્તા નંબર ફીલ્ડ આવશ્યક છે.',
        ],
        'no_of_user_prize' => [
            'required' => 'વપરાશકર્તા પુરસ્કારો નંબર ફીલ્ડ આવશ્યક છે.',
        ],
        'no_of_rank' => [
            'required' => 'રેન્ક ફીલ્ડની સંખ્યા જરૂરી છે.',
        ],
        'total_prize' => [
            'required' => 'કુલ પુરસ્કારો ફીલ્ડ જરૂરી છે.',
        ],
        'category_id' => [
            'required' => 'કેટેગરી ફીલ્ડ જરૂરી છે.',
        ],
        'contest_id' => [
            'required' => 'સ્પર્ધાનું ફીલ્ડ જરૂરી છે.',
        ],
        'question' => [
            'required' => 'પ્રશ્ન ફીલ્ડ જરૂરી છે.',
        ],
        'question_type' => [
            'required' => 'પ્રશ્ન પ્રકાર ફીલ્ડ આવશ્યક છે.',
        ],
        'option_a' => [
            'required' => 'એ ફીલ્ડ આવશ્યક છે.',
        ],

        'option_b' => [
            'required' => 'બી ફીલ્ડ આવશ્યક છે.',
        ],
        'note' => [
            'required' => 'ટિપ્પણી ફીલ્ડ આવશ્યક છે.',
        ],
        'answer' => [
            'required' => 'જવાબ ફીલ્ડ જરૂરી છે.',
        ],
        'earning_point' => [
            'required' => 'નંબર ફીલ્ડ જરૂરી છે.',
        ],
        'earning_amount' => [
            'required' => 'રકમ ફીલ્ડ જરૂરી છે.',
        ],
        'currency' => [
            'required' => 'વર્તમાન ફીલ્ડ જરૂરી છે.',
        ],
        'min_earning_point' => [
            'required' => 'ન્યૂનતમ ઉપાડ પોઈન્ટ્સ ફીલ્ડ આવશ્યક છે.',
        ],
        'daily_refer_limit' => [
            'required' => 'દૈનિક સંદર્ભ મર્યાદા ફીલ્ડ આવશ્યક છે.',
        ],
        'wallet_withdraw_visibility' => [
            'required' => 'વૉલેટ વિઝિબિલિટી પાછી ખેંચે છે ફીલ્ડ જરૂરી છે.',
        ],
        'Level' => [
            'required' => 'લેવલ ફીલ્ડ જરૂરી છે.',
        ],
        'Registration' => [
            'required' => 'નોંધણી ફીલ્ડ જરૂરી છે.',
        ],
        'ReferUser' => [
            'required' => 'રેફર યુઝર ફીલ્ડ જરૂરી છે.',
        ],
        '1' => [
            'required' => '1 ફીલ્ડ જરૂરી છે.',
        ],
        '2' => [
            'required' => '2 ફીલ્ડ જરૂરી છે.',
        ],
        '3' => [
            'required' => '3 ફીલ્ડ જરૂરી છે.',
        ],
        '4' => [
            'required' => '4 ફીલ્ડ જરૂરી છે.',
        ],
        '5' => [
            'required' => '5 ફીલ્ડ જરૂરી છે.',
        ],
        '6' => [
            'required' => '6 ફીલ્ડ જરૂરી છે.',
        ],
        'Day-1' => [
            'required' => 'દિવસ-1 ફીલ્ડ જરૂરી છે.',
        ],
        'Day-2' => [
            'required' => 'દિવસ-2 ફીલ્ડ જરૂરી છે.',
        ],
        'Day-3' => [
            'required' => 'દિવસ-3 ફીલ્ડ જરૂરી છે.',
        ],
        'Day-4' => [
            'required' => 'દિવસ-4 ફીલ્ડ જરૂરી છે.',
        ],
        'Day-5' => [
            'required' => 'દિવસ-5 ફીલ્ડ જરૂરી છે.',
        ],
        'Day-6' => [
            'required' => 'દિવસ-6 ફીલ્ડ જરૂરી છે.',
        ],
        'Day-7' => [
            'required' => 'દિવસ-7 ફીલ્ડ જરૂરી છે.',
        ],
        'free-coin' => [
            'required' => 'ફ્રી-કોઈન ફીલ્ડ જરૂરી છે.',
        ],
        'level_order' => [
            'required' => 'લેવલ ઓર્ડર નંબર ફીલ્ડ આવશ્યક છે.',
        ],
        'score' => [
            'required' => 'સ્કોર ફીલ્ડ જરૂરી છે.',
        ],
        'total_question' => [
            'required' => 'કુલ પ્રશ્ન ફીલ્ડ જરૂરી છે.',
        ],
        'win_question_count' => [
            'required' => 'આગલા સ્તરનો પ્રશ્ન નંબર URL ફીલ્ડ આવશ્યક છે.',
        ],
        'headings' => [
            'required' => 'શીર્ષક ફીલ્ડ જરૂરી છે.',
        ],
        'contents' => [
            'required' => 'સંદેશ ફીલ્ડ જરૂરી છે.',
        ],
        'big_picture' => [
            'max' =>  'છબીનું કદ :max kilobytes કરતાં મોટું ન હોવું જોઈએ.',
            'required' => 'છબી ફીલ્ડ આવશ્યક છે.',
        ],
        'onesignal_apid' => [
            'required' => 'OneSignal એપ્લિકેશન ID ફીલ્ડ આવશ્યક છે.',
        ],
        'onesignal_rest_key' => [
            'required' => 'OneSignal રેસ્ટ કી ફીલ્ડ જરૂરી છે.',
        ],
        'question_level_master_id' => [
            'required' => 'પ્રશ્ન સ્તર ફીલ્ડ આવશ્યક છે.',
        ],
        'app_name' => [
            'required' => 'એપ્લિકેશન નામ ફીલ્ડ આવશ્યક છે.',
        ],
        'host_email' => [
            'required' => 'હોસ્ટ ઈમેલ ફીલ્ડ જરૂરી છે.',
        ],
        'app_version' => [
            'required' => 'એપ્લિકેશન સંસ્કરણ ફીલ્ડ આવશ્યક છે.',
        ],
        'Author' => [
            'required' => 'લેખક ફીલ્ડ જરૂરી છે.',
        ],
        'contact' => [
            'required' => 'સંપર્ક ફીલ્ડ જરૂરી છે.',
        ],
        'app_desripation' => [
            'required' => 'એપ્લિકેશન વર્ણન ફીલ્ડ આવશ્યક છે.',
        ],
        'instrucation' => [
            'required' => 'સૂચના ફીલ્ડ જરૂરી છે.',
        ],
        'privacy_policy' => [
            'required' => 'ગોપનીયતા નીતિ ફીલ્ડ આવશ્યક છે.',
        ],
        'app_logo' => [
            'required' => 'એપ્લિકેશન ઈમેજ ફીલ્ડ જરૂરી છે.',
        ],
        'website' => [
            'required' => 'વેબસાઇટ ફીલ્ડ જરૂરી છે.',
        ],
        'confirm_password' => [
            'required' => 'પાસવર્ડ કન્ફર્મ કરો ફીલ્ડ જરૂરી છે.',
        ],
        'payment_1' => [
            'required' => '1 ચુકવણી નામ ફીલ્ડ આવશ્યક છે',
        ],
        'payment_2' => [
            'required' => '2 ચુકવણી નામ ફીલ્ડ આવશ્યક છે',
        ],
        'payment_3' => [
            'required' => '3 ચુકવણી નામ ફીલ્ડ આવશ્યક છે',
        ],
        'payment_4' => [
            'required' => '4 ચુકવણી નામ ફીલ્ડ આવશ્યક છે',
        ],
        'payment_5' => [
            'required' => '5 ચુકવણી નામ ફીલ્ડ આવશ્યક છે',
        ],
        'status' => [
            'required' => 'SMTP સક્ષમ છે ફીલ્ડ આવશ્યક છે.',
        ],
        'host' => [
            'required' => 'યજમાન ક્ષેત્ર જરૂરી છે.',
        ],
        'port' => [
            'required' => 'પોર્ટ ફીલ્ડ જરૂરી છે.',
        ],
        'protocol' => [
            'required' => 'શિષ્ટાચાર ફીલ્ડ જરૂરી છે.',
        ],
        'user' => [
            'required' => 'વપરાશકર્તા નામ ફીલ્ડ આવશ્યક છે.',
        ],
        'pass' => [
            'required' => 'પાસવર્ડ ફીલ્ડ જરૂરી છે.',
        ],
        'from_name' => [
            'required' => 'નામ પરથી ફીલ્ડ જરૂરી છે.',
        ],
        'from_email' => [
            'required' => 'ફ્રોમ ઈ-મેલ ફીલ્ડ જરૂરી છે.',
        ],
        'currency_code' => [
            'required' => 'ચલણ કોડ ફીલ્ડ આવશ્યક છે.',
        ],
        'currency' => [
            'required' => 'ચલણ નામ ફીલ્ડ આવશ્યક છે.',
        ],
        'publisher_id' => [
            'required' => 'પ્રકાશક ID ફીલ્ડ આવશ્યક છે.',
        ],
        'banner_adid' => [
            'required' => 'બેનર એડ ID ફીલ્ડ આવશ્યક છે.',
        ],
        'interstital_adid' => [
            'required' => 'ઇન્ટર્સ્ટિશલ એડ ID ફીલ્ડ આવશ્યક છે.',
        ],
        'native_adid' => [
            'required' => 'મૂળભૂત જાહેરાત ID ફીલ્ડ આવશ્યક છે.',
        ],
        'reward_adid' => [
            'required' => 'બાઉન્ટી વિડિયો એડ ID ફીલ્ડ આવશ્યક છે.',
        ],
        'fb_native_id' => [
            'required' => 'મૂળ ફીલ્ડ જરૂરી છે.',
        ],
        'fb_banner_id' => [
            'required' => 'બેનર ફીલ્ડ જરૂરી છે.',
        ],
        'fb_interstiatial_id' => [
            'required' => 'મધ્યમ ક્ષેત્ર જરૂરી છે.',
        ],
        'fb_rewardvideo_id' => [
            'required' => 'પુરસ્કાર વિડિઓ ફીલ્ડ આવશ્યક છે.',
        ],
        'fb_native_full_id' => [
            'required' => 'નેટિવ કમ્પ્લીટ ફીલ્ડ આવશ્યક છે.',
        ],
        'purchase_code' => [
            'required' => 'ખરીદી કોડ ફીલ્ડ આવશ્યક છે.',
        ],
        'package_name' => [
            'required' => 'પેકેજ નામ ફીલ્ડ જરૂરી છે.',
        ],
        'coin' => [
            'required' => 'સિક્કા ક્ષેત્ર જરૂરી છે.',
        ],
        'product_package' => [
            'required' => 'પ્રોડક્ટ પેકેજ ફીલ્ડ જરૂરી છે.',
        ],
        'price' => [
            'required' => 'કિંમત ફીલ્ડ જરૂરી છે.',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
