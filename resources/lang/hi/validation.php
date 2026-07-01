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
            'required' => 'ईमेल फ़ील्ड आवश्यक है.',
        ],
        'password' => [
            'required' => 'पासवर्ड फ़ील्ड आवश्यक है.',
        ],
        'fullname' => [
            'required' => 'नाम फ़ील्ड आवश्यक है.',
        ],
        'username' => [
            'required' => 'उपयोगकर्ता नाम फ़ील्ड आवश्यक है.',
        ],
        'profile_img' => [
            'max' =>  'छवि का आकार :max kilobytes से बड़ा नहीं होना चाहिए.',
            'required' => 'छवि फ़ील्ड आवश्यक है.',
        ],
        'mobile_number' => [
            'required' => 'मोबाइल नंबर फ़ील्ड आवश्यक है.',
        ],
        'image' => [
            'max' =>  'छवि का आकार :max kilobytes से बड़ा नहीं होना चाहिए.',
            'required' => 'छवि फ़ील्ड आवश्यक है.',
        ],
        'level_name' => [
            'required' => 'नाम फ़ील्ड आवश्यक है.',
        ],
        'level_order' => [
            'required' => 'स्तर आदेश संख्या फ़ील्ड आवश्यक है.',
        ],
        'level_id' => [
            'required' => 'स्तर फ़ील्ड आवश्यक है.',
        ],
        'name' => [
            'required' => 'नाम फ़ील्ड आवश्यक है.',
        ],
        'start_date' => [
            'required' => 'आरंभ करने की तिथि फ़ील्ड आवश्यक है.',
        ],
        'end_date' => [
            'required' => 'अंतिम तिथि फ़ील्ड आवश्यक है.',
        ],
        'price' => [
            'required' => 'प्रवेश शुल्क फ़ील्ड आवश्यक है.',
        ],
        'no_of_user' => [
            'required' => 'उपयोगकर्ता की संख्या फ़ील्ड आवश्यक है.',
        ],
        'no_of_user_prize' => [
            'required' => 'उपयोगकर्ता पुरस्कार की संख्या फ़ील्ड आवश्यक है.',
        ],
        'no_of_rank' => [
            'required' => 'रैंक की संख्या फ़ील्ड आवश्यक है.',
        ],
        'total_prize' => [
            'required' => 'कुल पुरस्कार फ़ील्ड आवश्यक है.',
        ],
        'category_id' => [
            'required' => 'श्रेणी फ़ील्ड आवश्यक है.',
        ],
        'contest_id' => [
            'required' => 'प्रतियोगिता फ़ील्ड आवश्यक है.',
        ],
        'question' => [
            'required' => 'प्रश्न फ़ील्ड आवश्यक है.',
        ],
        'question_type' => [
            'required' => 'प्रश्न प्रकार फ़ील्ड आवश्यक है.',
        ],
        'option_a' => [
            'required' => 'ए फ़ील्ड आवश्यक है.',
        ],

        'option_b' => [
            'required' => 'बी फ़ील्ड आवश्यक है.',
        ],
        'note' => [
            'required' => 'टिप्पणी फ़ील्ड आवश्यक है.',
        ],
        'answer' => [
            'required' => 'उत्तर फ़ील्ड आवश्यक है.',
        ],
        'earning_point' => [
            'required' => 'अंक फ़ील्ड आवश्यक है.',
        ],
        'earning_amount' => [
            'required' => 'राशि फ़ील्ड आवश्यक है.',
        ],
        'currency' => [
            'required' => 'वर्तमान फ़ील्ड आवश्यक है.',
        ],
        'min_earning_point' => [
            'required' => 'न्यूनतम निकासी अंक फ़ील्ड आवश्यक है.',
        ],
        'daily_refer_limit' => [
            'required' => 'दैनिक संदर्भ सीमा फ़ील्ड आवश्यक है.',
        ],
        'wallet_withdraw_visibility' => [
            'required' => 'बटुआ दृश्यता वापस लेता है फ़ील्ड आवश्यक है.',
        ],
        'Level' => [
            'required' => 'स्तर फ़ील्ड आवश्यक है.',
        ],
        'Registration' => [
            'required' => 'पंजीकरण फ़ील्ड आवश्यक है.',
        ],
        'ReferUser' => [
            'required' => 'रेफ़र यूज़र फ़ील्ड आवश्यक है.',
        ],
        '1' => [
            'required' => '1 फ़ील्ड आवश्यक है.',
        ],
        '2' => [
            'required' => '2 फ़ील्ड आवश्यक है.',
        ],
        '3' => [
            'required' => '3 फ़ील्ड आवश्यक है.',
        ],
        '4' => [
            'required' => '4 फ़ील्ड आवश्यक है.',
        ],
        '5' => [
            'required' => '5 फ़ील्ड आवश्यक है.',
        ],
        '6' => [
            'required' => '6 फ़ील्ड आवश्यक है.',
        ],
        'Day-1' => [
            'required' => 'दिन-1 फ़ील्ड आवश्यक है.',
        ],
        'Day-2' => [
            'required' => 'दिन-2 फ़ील्ड आवश्यक है.',
        ],
        'Day-3' => [
            'required' => 'दिन-3 फ़ील्ड आवश्यक है.',
        ],
        'Day-4' => [
            'required' => 'दिन-4 फ़ील्ड आवश्यक है.',
        ],
        'Day-5' => [
            'required' => 'दिन-5 फ़ील्ड आवश्यक है.',
        ],
        'Day-6' => [
            'required' => 'दिन-6 फ़ील्ड आवश्यक है.',
        ],
        'Day-7' => [
            'required' => 'दिन-7 फ़ील्ड आवश्यक है.',
        ],
        'free-coin' => [
            'required' => 'फ्री-सिक्का फ़ील्ड आवश्यक है.',
        ],
        'level_order' => [
            'required' => 'स्तर आदेश संख्या फ़ील्ड आवश्यक है.',
        ],
        'score' => [
            'required' => 'स्कोर फ़ील्ड आवश्यक है.',
        ],
        'total_question' => [
            'required' => 'कुल प्रश्नम फ़ील्ड आवश्यक है.',
        ],
        'win_question_count' => [
            'required' => 'अगले स्तर की प्रश्न संख्या यूआरएल फ़ील्ड आवश्यक है.',
        ],
        'headings' => [
            'required' => 'शीर्षक फ़ील्ड आवश्यक है.',
        ],
        'contents' => [
            'required' => 'संदेश फ़ील्ड आवश्यक है.',
        ],
        'big_picture' => [
            'max' =>  'छवि का आकार :max kilobytes से बड़ा नहीं होना चाहिए.',
            'image' => 'छवि होनी चाहिए।',
        ],
        'onesignal_apid' => [
            'required' => 'वनसिग्नल ऐप आईडी फ़ील्ड आवश्यक है.',
        ],
        'onesignal_rest_key' => [
            'required' => 'वनसिग्नल रेस्ट की फ़ील्ड आवश्यक है.',
        ],
        'question_level_master_id' => [
            'required' => 'प्रश्न स्तर फ़ील्ड आवश्यक है.',
        ],
        'app_name' => [
            'required' => 'एप्लिकेशन का नाम फ़ील्ड आवश्यक है.',
        ],
        'host_email' => [
            'required' => 'होस्ट ईमेल फ़ील्ड आवश्यक है.',
        ],
        'app_version' => [
            'required' => 'एप्लिकेशन वेरीज़न फ़ील्ड आवश्यक है.',
        ],
        'Author' => [
            'required' => 'लेखक फ़ील्ड आवश्यक है.',
        ],
        'contact' => [
            'required' => 'संपर्क करें फ़ील्ड आवश्यक है.',
        ],
        'app_desripation' => [
            'required' => 'एप्लिकेशन विवरण फ़ील्ड आवश्यक है.',
        ],
        'instrucation' => [
            'required' => 'अनुदेश फ़ील्ड आवश्यक है.',
        ],
        'privacy_policy' => [
            'required' => 'गोपनीयता नीति फ़ील्ड आवश्यक है.',
        ],
        'app_logo' => [
            'required' => 'एप्लिकेशन छवि फ़ील्ड आवश्यक है.',
        ],
        'website' => [
            'required' => 'वेबसाइट फ़ील्ड आवश्यक है.',
        ],
        'confirm_password' => [
            'required' => 'पासवर्ड की पुष्टि कीजिये फ़ील्ड आवश्यक है.',
        ],
        'payment_1' => [
            'required' => '1 भुगतान का नाम फ़ील्ड आवश्यक है.',
        ],
        'payment_2' => [
            'required' => '2 भुगतान का नाम फ़ील्ड आवश्यक है.',
        ],
        'payment_3' => [
            'required' => '3 भुगतान का नाम फ़ील्ड आवश्यक है.',
        ],
        'payment_4' => [
            'required' => '4 भुगतान का नाम फ़ील्ड आवश्यक है.',
        ],
        'payment_5' => [
            'required' => '5 भुगतान का नाम फ़ील्ड आवश्यक है.',
        ],
        'status' => [
            'required' => 'एसएमटीपी सक्रिय है फ़ील्ड आवश्यक है.',
        ],
        'host' => [
            'required' => 'मेज़बान फ़ील्ड आवश्यक है.',
        ],
        'port' => [
            'required' => 'पोर्ट फ़ील्ड आवश्यक है.',
        ],
        'protocol' => [
            'required' => 'शिष्टाचार फ़ील्ड आवश्यक है.',
        ],
        'user' => [
            'required' => 'उपयोगकर्ता नाम फ़ील्ड आवश्यक है.',
        ],
        'pass' => [
            'required' => 'पासवर्ड फ़ील्ड आवश्यक है.',
        ],
        'from_name' => [
            'required' => 'पासवर्ड फ़ील्ड आवश्यक है.',
        ],
        'from_email' => [
            'required' => 'ई-मेल से फ़ील्ड आवश्यक है.',
        ],
        'currency_code' => [
            'required' => 'मुद्रा कोड फ़ील्ड आवश्यक है.',
        ],
        'currency' => [
            'required' => 'मुद्रा का नाम फ़ील्ड आवश्यक है.',
        ],
        'publisher_id' => [
            'required' => 'प्रकाशक आईडी फ़ील्ड आवश्यक है.',
        ],
        'banner_adid' => [
            'required' => 'बैनर विज्ञापन आईडी फ़ील्ड आवश्यक है.',
        ],
        'interstital_adid' => [
            'required' => 'मध्यवर्ती विज्ञापन आईडी फ़ील्ड आवश्यक है.',
        ],
        'native_adid' => [
            'required' => 'मूल विज्ञापन आईडी फ़ील्ड आवश्यक है.',
        ],
        'reward_adid' => [
            'required' => 'इनाम वीडियो विज्ञापन आईडी फ़ील्ड आवश्यक है.',
        ],
        'fb_native_id' => [
            'required' => 'मूल निवासी फ़ील्ड आवश्यक है.',
        ],
        'fb_banner_id' => [
            'required' => 'बैनर फ़ील्ड आवश्यक है.',
        ],
        'fb_interstiatial_id' => [
            'required' => 'मध्य फ़ील्ड आवश्यक है.',
        ],
        'fb_rewardvideo_id' => [
            'required' => 'इनाम वीडियो फ़ील्ड आवश्यक है.',
        ],
        'fb_native_full_id' => [
            'required' => 'मूल निवासी पूर्ण फ़ील्ड आवश्यक है.',
        ],
        'purchase_code' => [
            'required' => 'खरीद कोड फ़ील्ड आवश्यक है.',
        ],
        'package_name' => [
            'required' => 'पैकेज का नाम फ़ील्ड आवश्यक है.',
        ],
        'coin' => [
            'required' => 'सिक्का फ़ील्ड आवश्यक है.',
        ],
        'product_package' => [
            'required' => 'उत्पाद पैकेज फ़ील्ड आवश्यक है.',
        ],
        'price' => [
            'required' => 'कीमत फ़ील्ड आवश्यक है.',
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
