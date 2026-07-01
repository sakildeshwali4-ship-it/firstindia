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
            'required' => 'حقل البريد الإلكتروني مطلوب.',
        ],
        'password' => [
            'required' => 'حقل كلمة المرور مطلوب.',
        ],
        'fullname' => [
            'required' => 'حقل الاسم مطلوب.',
        ],
        'username' => [
            'required' => 'مطلوب حقل اسم المستخدم.',
        ],
        'profile_img' => [
            'max' => 'حجم الصورة :max kilobytes لا ينبغي أن يكون أكبر من', 
            'required' => 'حقل الصورة مطلوب.',
        ],
        'mobile_number' => [
            'required' => 'رقم الجوال مطلوب',
        ],
        'image' => [
            'max' => 'حجم الصورة :max kilobytes لا ينبغي أن يكون أكبر من',
            'required' => 'حقل الصورة مطلوب.',
        ],
        'level_name' => [
            'required' => 'حقل الاسم مطلوب.',
        ],
        'level_order' => [
            'required' => 'مطلوب حقل رقم أمر المستوى.',
        ],
        'level_id' => [
            'required' => 'حقل المستوى مطلوب.',
        ],
        'name' => [
            'required' => 'حقل الاسم مطلوب.',
        ],
        'start_date' => [
            'required' => 'حقل تاريخ البدء مطلوب.',
        ],
        'end_date' => [
            'required' => 'حقل تاريخ الانتهاء مطلوب.',
        ],
        'price' => [
            'required' => 'مطلوب حقل رسوم الدخول.',
        ],
        'no_of_user' => [
            'required' => 'حقل رقم المستخدم مطلوب.',
        ],
        'no_of_user_prize' => [
            'required' => 'مطلوب حقل رقم مكافآت المستخدم.',
        ],
        'no_of_rank' => [
            'required' => 'عدد حقول الرتب المطلوبة.',
        ],
        'total_prize' => [
            'required' => 'مطلوب حقل إجمالي المكافآت.',
        ],
        'category_id' => [
            'required' => 'حقل الفئة مطلوب.',
        ],
        'contest_id' => [
            'required' => 'مجال المنافسة مطلوب.',
        ],
        'question' => [
            'required' => 'حقل السؤال مطلوب.',
        ],
        'question_type' => [
            'required' => 'حقل نوع السؤال مطلوب.',
        ],
        'option_a' => [
            'required' => 'مطلوب حقل.',
        ],

        'option_b' => [
            'required' => 'مطلوب حقل "ب".',
        ],
        'note' => [
            'required' => 'حقل التعليق مطلوب.',
        ],
        'answer' => [
            'required' => 'حقل الإجابة مطلوب.',
        ],
        'earning_point' => [
            'required' => 'حقل الرقم مطلوب.',
        ],
        'earning_amount' => [
            'required' => 'مطلوب حقل المبلغ.',
        ],
        'currency' => [
            'required' => 'الحقل الحالي مطلوب.',
        ],
        'min_earning_point' => [
            'required' => 'مطلوب حقل الحد الأدنى من نقاط السحب.',
        ],
        'daily_refer_limit' => [
            'required' => 'مطلوب حقل حد المرجع اليومي.',
        ],
        'wallet_withdraw_visibility' => [
            'required' => 'إظهار المحفظة حقل مطلوب.',
        ],
        'Level' => [
            'required' => 'حقل المستوى مطلوب.',
        ],
        'Registration' => [
            'required' => 'حقل التسجيل مطلوب.',
        ],
        'ReferUser' => [
            'required' => 'مطلوب حقل المستخدم المُحيل.',
        ],
        '1' => [
            'required' => '1 حقل مطلوب.',
        ],
        '2' => [
            'required' => '2 حقل مطلوب.',
        ],
        '3' => [
            'required' => '3 حقل مطلوب.',
        ],
        '4' => [
            'required' => '4 حقل مطلوب.',
        ],
        '5' => [
            'required' => '5 حقل مطلوب.',
        ],
        '6' => [
            'required' => '6 حقل مطلوب.',
        ],
        'Day-1' => [
            'required' => 'حقل اليوم الأول مطلوب.1.',
        ],
        'Day-2' => [
            'required' => 'مطلوب حقل اليوم 2.',
        ],
        'Day-3' => [
            'required' => 'مطلوب حقل اليوم 3.',
        ],
        'Day-4' => [
            'required' => 'مطلوب حقل اليوم 4.',
        ],
        'Day-5' => [
            'required' => 'مطلوب حقل اليوم الخامس.',
        ],
        'Day-6' => [
            'required' => 'مطلوب حقل اليوم 6.',
        ],
        'Day-7' => [
            'required' => 'مطلوب حقل اليوم 7.',
        ],
        'free-coin' => [
            'required' => 'مطلوب حقل عملة مجانية.',
        ],
        'level_order' => [
            'required' => 'مطلوب حقل رقم أمر المستوى.',
        ],
        'score' => [
            'required' => 'حقل النتيجة مطلوب.',
        ],
        'total_question' => [
            'required' => 'مطلوب حقل السؤال الإجمالي.',
        ],
        'win_question_count' => [
            'required' => 'حقل عنوان URL الخاص برقم سؤال المستوى التالي مطلوب.',
        ],
        'headings' => [
            'required' => 'حقل العنوان مطلوب.',
        ],
        'contents' => [
            'required' => 'حقل الرسالة مطلوب.',
        ],
        'big_picture' => [
            'max' => 'حجم الصورة :max kilobytes لا ينبغي أن يكون أكبر من',
            'required' => 'حقل الصورة مطلوب.',
        ],
        'onesignal_apid' => [
            'required' => 'مطلوب حقل معرف تطبيق OneSignal.',
        ],
        'onesignal_rest_key' => [
            'required' => 'مطلوب حقل OneSignal Rest Key.',
        ],
        'question_level_master_id' => [
            'required' => 'مطلوب حقل مستوى السؤال.',
        ],
        'app_name' => [
            'required' => 'حقل اسم التطبيق مطلوب.',
        ],
        'host_email' => [
            'required' => 'مطلوب حقل البريد الإلكتروني للمضيف.',
        ],
        'app_version' => [
            'required' => 'حقل إصدار التطبيق مطلوب.',
        ],
        'Author' => [
            'required' => 'حقل المؤلف مطلوب.',
        ],
        'contact' => [
            'required' => 'حقل الاتصال مطلوب.',
        ],
        'app_desripation' => [
            'required' => 'مطلوب حقل وصف التطبيق.',
        ],
        'instrucation' => [
            'required' => 'حقل الإعلام مطلوب.',
        ],
        'privacy_policy' => [
            'required' => 'مطلوب حقل سياسة الخصوصية.',
        ],
        'app_logo' => [
            'required' => 'مطلوب حقل صورة التطبيق.',
        ],
        'website' => [
            'required' => 'حقل الموقع مطلوب.',
        ],
        'confirm_password' => [
            'required' => 'مطلوب حقل تأكيد كلمة المرور.',
        ],
        'payment_1' => [
            'required' => '1 حقل اسم الدفع مطلوب',
        ],
        'payment_2' => [
            'required' => '2 قل اسم الدفع مطلوب',
        ],
        'payment_3' => [
            'required' => '3 قل اسم الدفع مطلوب',
        ],
        'payment_4' => [
            'required' => '4 قل اسم الدفع مطلوب',
        ],
        'payment_5' => [
            'required' => '5 قل اسم الدفع مطلوب',
        ],
        'status' => [
            'required' => 'مطلوب حقل تمكين SMTP.',
        ],
        'host' => [
            'required' => 'المنطقة المضيفة مطلوبة.',
        ],
        'port' => [
            'required' => 'حقل المنفذ مطلوب.',
        ],
        'protocol' => [
            'required' => 'مطلوب حقل آداب.',
        ],
        'user' => [
            'required' => 'مطلوب حقل اسم المستخدم.',
        ],
        'pass' => [
            'required' => 'حقل كلمة المرور مطلوب.',
        ],
        'from_name' => [
            'required' => 'مطلوب حقل من الاسم.',
        ],
        'from_email' => [
            'required' => 'مطلوب من حقل البريد الإلكتروني.',
        ],
        'currency_code' => [
            'required' => 'حقل كود العملة مطلوب.',
        ],
        'currency' => [
            'required' => 'حقل اسم العملة مطلوب.',
        ],
        'publisher_id' => [
            'required' => 'حقل معرف الناشر مطلوب.',
        ],
        'banner_adid' => [
            'required' => 'مطلوب حقل معرف إعلان البانر.',
        ],
        'interstital_adid' => [
            'required' => 'مطلوب حقل معرف الإعلان البيني.',
        ],
        'native_adid' => [
            'required' => 'مطلوب حقل معرف الإعلان الأساسي.',
        ],
        'reward_adid' => [
            'required' => 'مطلوب حقل Bounty Video Ad ID.',
        ],
        'fb_native_id' => [
            'required' => 'الحقل الأصلي مطلوب.',
        ],
        'fb_banner_id' => [
            'required' => 'حقل البانر مطلوب.',
        ],
        'fb_interstiatial_id' => [
            'required' => 'الحقل الأوسط مطلوب.',
        ],
        'fb_rewardvideo_id' => [
            'required' => 'مطلوب حقل مكافأة الفيديو.',
        ],
        'fb_native_full_id' => [
            'required' => 'مطلوب حقل كامل أصلي.',
        ],
        'purchase_code' => [
            'required' => 'مطلوب حقل رمز الشراء.',
        ],
        'package_name' => [
            'required' => 'حقل اسم الحزمة مطلوب.',
        ],
        'coin' => [
            'required' => 'مطلوب حقل عملة.',
        ],
        'product_package' => [
            'required' => 'حقل حزمة المنتج مطلوب.',
        ],
        'price' => [
            'required' => 'حقل السعر مطلوب.',
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
