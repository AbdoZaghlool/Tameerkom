<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | such as the size rules. Feel free to tweak each of these messages.
    |
    */
    'accepted'             => 'يجب قبول :attribute',
    'active_url'           => ':attribute لا يُمثّل رابطًا صحيحًا',
    'after'                => 'يجب على :attribute أن يكون تاريخًا لاحقًا للتاريخ :date.',
    'after_or_equal'       => ':attribute يجب أن يكون تاريخاً لاحقاً أو مطابقاً للتاريخ :date.',
    'alpha'                => 'يجب أن لا يحتوي :attribute سوى على حروف',
    'alpha_dash'           => 'يجب أن لا يحتوي :attribute على حروف، أرقام ومطّات.',
    'alpha_num'            => 'يجب أن يحتوي :attribute على حروفٍ وأرقامٍ فقط',
    'array'                => 'يجب أن يكون :attribute ًمصفوفة',
    'before'               => 'يجب على :attribute أن يكون تاريخًا سابقًا للتاريخ :date.',
    'before_or_equal'      => ':attribute يجب أن يكون تاريخا سابقا أو مطابقا للتاريخ :date',
    'between'              => [
        'numeric' => 'يجب أن تكون قيمة :attribute بين :min و :max.',
        'file'    => 'يجب أن يكون حجم الملف :attribute بين :min و :max كيلوبايت.',
        'string'  => 'يجب أن يكون عدد حروف النّص :attribute بين :min و :max',
        'array'   => 'يجب أن يحتوي :attribute على عدد من العناصر بين :min و :max',
    ],
    'gt'                   => [
        'numeric' => ' :attribute يجب ان يكون اكبر من :value.',
        'file'    => ':attribute يجب ان يكون اكبر من :value كيلوبايت.',
        'string'  => ':attribute يجب ان يكون اكثر من :value حرف.',
        'array'   => ':attribute يجب ان يكون اكثر من :value عنصر.',
    ],
    'boolean'              => 'يجب أن تكون قيمة :attribute إما true أو false ',
    'confirmed'            => 'حقل التأكيد غير مُطابق للحقل :attribute',
    'date'                 => ':attribute ليس تاريخًا صحيحًا',
    'date_format'          => 'لا يتوافق :attribute مع الشكل :format.',
    'different'            => 'يجب أن يكون الحقلان :attribute و :other مُختلفان',
    'digits'               => 'يجب أن يحتوي :attribute على :digits رقمًا/أرقام',
    'digits_between'       => 'يجب أن يحتوي :attribute بين :min و :max رقمًا/أرقام ',
    'dimensions'           => 'الـ :attribute يحتوي على أبعاد صورة غير صالحة.',
    'distinct'             => 'للحقل :attribute قيمة مُكرّرة.',
    'email'                => 'يجب أن يكون :attribute عنوان بريد إلكتروني صحيح البُنية',
    'exists'               => ':attribute غير صحيح',
    'file'                 => 'الـ :attribute يجب أن يكون ملفا.',
    'filled'               => ':attribute إجباري',
    'image'                => 'يجب أن يكون :attribute صورةً',
    'in'                   => ':attribute يجب ان يكون في :values فقط',
    'in_array'             => ':attribute غير موجود في :other.',
    'integer'              => 'يجب أن يكون :attribute عددًا صحيحًا',
    'ip'                   => 'يجب أن يكون :attribute عنوان IP صحيحًا',
    'ipv4'                 => 'يجب أن يكون :attribute عنوان IPv4 صحيحًا.',
    'ipv6'                 => 'يجب أن يكون :attribute عنوان IPv6 صحيحًا.',
    'json'                 => 'يجب أن يكون :attribute نصآ من نوع JSON.',
    'max'                  => [
        'numeric' => 'يجب أن تكون قيمة :attribute مساوية أو أصغر لـ :max.',
        'file'    => 'يجب أن لا يتجاوز حجم الملف :attribute :max كيلوبايت',
        'string'  => 'يجب أن لا يتجاوز طول النّص :attribute :max حروفٍ/حرفًا',
        'array'   => 'يجب أن لا يحتوي :attribute على أكثر من :max عناصر/عنصر.',
    ],
    'mimes'                => 'يجب أن يكون ملفًا من نوع : :values.',
    'mimetypes'            => 'يجب أن يكون ملفًا من نوع : :values.',
    'min'                  => [
        'numeric' => 'يجب أن تكون قيمة :attribute مساوية أو أكبر لـ :min.',
        'file'    => 'يجب أن يكون حجم الملف :attribute على الأقل :min كيلوبايت',
        'string'  => 'يجب أن يكون طول النص :attribute على الأقل :min حروفٍ/حرفًا',
        'array'   => 'يجب أن يحتوي :attribute على الأقل على :min عُنصرًا/عناصر',
    ],
    'not_in'               => ':attribute يجب ان لا يحتوي على :values',
    'numeric'              => 'يجب على :attribute أن يكون رقمًا',
    'present'              => 'يجب تقديم :attribute',
    'regex'                => 'صيغة :attribute .غير صحيحة',
    'required'             => ':attribute مطلوب.',
    'required_if'          => ':attribute مطلوب في حال ما إذا كان :other يساوي :value.',
    'required_unless'      => ':attribute مطلوب في حال ما لم يكن :other يساوي :values.',
    'required_with'        => ':attribute مطلوب إذا توفّر :values.',
    'required_with_all'    => ':attribute مطلوب إذا توفّر :values.',
    'required_without'     => ':attribute مطلوب إذا لم يتوفّر :values.',
    'required_without_all' => ':attribute مطلوب إذا لم يتوفّر :values.',
    'same'                 => 'يجب أن يتطابق :attribute مع :other',
    'size'                 => [
        'numeric' => 'يجب أن تكون قيمة :attribute مساوية لـ :size',
        'file'    => 'يجب أن يكون حجم الملف :attribute :size كيلوبايت',
        'string'  => 'يجب أن يحتوي النص :attribute على :size حروفٍ/حرفًا بالظبط',
        'array'   => 'يجب أن يحتوي :attribute على :size عنصرٍ/عناصر بالظبط',
    ],
    'string'               => 'يجب أن يكون :attribute نصآ.',
    'starts_with'          => 'يجب ان يبدأ :attribute بقيمة :values.',
    'timezone'             => 'يجب أن يكون :attribute نطاقًا زمنيًا صحيحًا',
    'unique'               => 'قيمة :attribute مُستخدمة من قبل',
    'uploaded'             => 'فشل في تحميل الـ :attribute',
    'url'                  => 'صيغة الرابط :attribute غير صحيحة',
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
    'custom'               => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],
    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */
    'attributes'           => [
        'name'                  => 'الاسم',
        'family_commission'     => 'عمولة الاسر',
        'min_value_withdrow_driver'     => 'الحد الادني للسحب',
        'min_value_withdrow_family'     => 'الحد الادني للسحب',
        'tax'     => 'الضريبة',
        'driver_commission'     => 'عمولة السائقين',
        'accept_order_time'     => 'وقت انتظار الطلب بدون عرض السائقين',
        "brief"                   => "النبذة",
        "distance"                   => "نطاق البحث",
        "work_start_at"                   => "وقت البداية",
        "work_end_at"                   => "وقت الانتهاء",
        "latitude"                   => "خط العرض",
        "longitude"                   => "خط الطول",
        "service_id"                   => "الخدمات",
        'username'              => 'اسم المُستخدم',
        'username2'              => 'اسم المُستخدم',
        'username3'              => 'اسم المُستخدم',
        'email'                 => 'البريد الالكتروني',
        'firstname'            => 'الاسم',
        'lastname'             => 'اسم العائلة',
        'password'              => 'كلمة السر',
        'password_confirmation' => 'تأكيد كلمة السر',
        'city'                  => 'المدينة',
        'country'               => 'الدولة',
        'address'               => 'العنوان',
        'phone_number'                 => 'الهاتف',
        'mobile'                => 'الجوال',
        'age'                   => 'العمر',
        'sex'                   => 'الجنس',
        'gender'                => 'النوع',
        'day'                   => 'اليوم',
        'month'                 => 'الشهر',
        'year'                  => 'السنة',
        'hour'                  => 'ساعة',
        'minute'                => 'دقيقة',
        'second'                => 'ثانية',
        'title'                 => 'اللقب',
        'content'               => 'المُحتوى',
        'description'           => 'الوصف',
        'excerpt'               => 'المُلخص',
        'date'                  => 'التاريخ',
        'date2'                  => 'التاريخ',
        'date3'                  => 'التاريخ',
        'time'                  => 'الوقت',
        'available'             => 'مُتاح',
        'size'                  => 'الحجم',
        'privacyConditions'                  => 'شروط الخصوصية',
        'register-country'                  => 'الدولة',
        'register-grade'                  => 'المرحلة الدراسية ',
         'message'                  => 'الرسالة ',
         'identity_number'                  => 'رقم الهوية ',
         'tax_number'                  => 'الرقم الضريبي ',
         'insurance_number'                  => 'الرقم التأميني ',
         'commission'                  => 'العمولة ',
         'name.ar'                  => 'اسم الموقع ',
         'name.en'                  => 'اسم الموقع  ',
         'name_social.ar'                  => 'الاسم',
         'name_social.en'                  => 'الاسم ',
         'fav_icon'                  => 'أيقونة ',
         'logo_header'                  => 'لوجو أعلى الصفحة',
         'logo_footer'                  => 'لوجو أسفل الصفحة ',
         'type'                  => 'النوع ',
         'link'                  => 'الرابط ',
         'icon'                  => 'أيقونة ',
         'name_bank.ar'                  => 'اسم البنك',
         'bank_user_name'                  => 'اسم صاحب الحساب',
         'name_bank.en'                  => 'اسم البنك ',
         'account_number'                  => 'رقم الحساب ',
         'account_number2'                  => 'رقم الحساب ',
         'account_number3'                  => 'رقم الحساب ',
         'account_id'                  => 'الايبان ',
         'username.ar'                  => 'اسم المستخدم ',
         'username.en'                  => 'اسم المستخدم ',
         'image'                  => 'الصورة',
         'image2'                  => 'الصورة',
         'image3'                  => 'الصورة',
         'text.ar'                  => 'المحتوي',
         'text.en'                  => 'المحتوي',
         'page_name.ar'                  => 'اسم الصفحة',
         'page_name.en'                  => 'اسم الصفحة',
         'order'                  => 'ترتيب العرض',
         'offer.ar'                  => 'الاسم',
         'offer.en'                  => 'الاسم',
         'place'                  => 'المكان',
         'duration'                  => 'المدة',
         'duration_type'                  => 'نوع المدة',
         'price'                  => 'السعر',
         'products_attributes.*.ar'                  => 'المواصفات',
         'products_attributes.*.en'                  => 'المواصفات',
         'attributes_type'                  => 'حقل المواصفات',
         'main_section.ar'                  => 'القسم الرئيسي',
         'main_section.en'                  => 'القسم الرئيسي',
         'main_section'                  => 'القسم الرئيسي',
         'sub_section.ar'                  => 'القسم الفرعي',
         'sub_section.en'                  => 'القسم الفرعي',
         'desc.en'                  => 'الوصف',
         'desc.ar'                  => 'الوصف',
         'keywords.en'                  => 'كلمات دلالية',
         'keywords.ar'                  => 'كلمات دلالية',
         'sub_section'                  => 'القسم الفرعي',
         'subSub_section'                  => 'القسم الفرعي',
         'category_attributes'                  => 'المواصفات',
         'blacklist'                  => 'الحظر',
         'active'                  => 'التفعيل',
         'images.*'                  => 'الصور',
         'images'                  => 'الصور',
         'video'                  => 'الفيديو',
         'sort'                  => 'الترتيب',
         'subscription_type'                  => 'نوع الاعلان',
         'ads_name'                  => 'اسم الاعلان',
         'end_duration'                  => 'تاريخ الانتهاء',
         'all_attributes.*'                  => 'المواصفات',
         'attributes_required'                  => 'المواصفات',
         'end_special'                  => 'تاريخ الانتهاء',
         'phone'                  => 'رقم الهاتف',
         'role'                  => 'المجموعة',
         'code'                  => 'الكود',
         'code1'                  => 'الكود',
         'code2'                  => 'الكود',
         'subscription'                  => 'مدة الاعلان',
         'category'                  => 'القسم',
         'type_ads'                  => 'نوع الاعلان',
        //  'latitude'   => 'الخريطة',
         'sub_category'   => 'القسم الفرعي',
        'register_confirm'   => 'الموافقة على الشروط',
        'amount'   => 'المبلغ',
        'ads_id'   => 'رقم الاعلان',
        'ads_id2'   => 'رقم الاعلان',
        'ads_id3'   => 'رقم الاعلان',
        'reference_number'   => 'الرقم المرجعي',
        'reference_number2'   => 'الرقم المرجعي',
        'reference_number3'   => 'الرقم المرجعي',
        'bank_name'   => 'البنك',
        'bank_name2'   => 'البنك',
        'bank_name3'   => 'البنك',
        'old_password'   => 'كلمة المرور القديمة',
        'user_id'   => 'رقم المستخدم',
        'user_id2'   => 'رقم المستخدم',
        'user_id3'   => 'رقم المستخدم',
        'offer'   => 'الباقات',
        'offer2'   => 'الباقات',
        'subscription_offer'   => 'الباقات',
        'special_offer'   => 'الباقات',
        'ads_type'   => 'نوع الدفع',
        'ads_user'=> 'صاحب الاعلان',
        'comment'=>'التعليق',
        'rate'=>'التقييم',
        'ad_id'=>'رقم الاعلان',
        'sender'=>'اسم المرسل',
        'url'=>'المسار',
        "attribute_name"=>"الاسم",

        "ads_width"    => "العرض",
        "ads_height"    => "الطول",
        "section_width"    => "العرض",
        "section_height"    => "الطول",
        "home_width"    => "العرض",
        "home_height"    => "الطول",
        "main_image"    => "الصورة الرئيسية",
        "g-recaptcha-response"    => "حقل التحقق",
        "region"    => "المنطقة",
        "user_type"    => "نوع الحساب",
        "company_name"    => "اسم الشركة",
        "commercial_number"    => "رقم السجل التجاري",
        "commercial_end_date"    => "تاريخ انتهاء السجل التجاري",
        "source.name"    => "اسم المستفيد",
        "source.number"    => "رقم الحساب",
        "source.month"    => "رقم الشهر",
        "source.year"    => "السنة الحالية",
        "source.username"    => "اسم المستفيد",
        "ads_image_number"    => "عدد صور الداخلية للاعلان",
        "mazad_time"    => "المدة الزمنية للمزاد",
        "mazad_number"    => "عدد مرات المزايدة في اليوم الواحد",
        "static_price_time"    => "المدة الزمنية للسعر الثابت",
        "pulling_out_numbers"    => "عدد مرات الانسحاب",
        "offer_time_for_saler"    => "المدة الزمنية لتقديم عرض",
        "days_num_to_receive"    => "عدد الايام لاستلام المنتج من المعرض",
        "deposit_type"    => "نوع العربون",
        "deposit_price"    => "سعر العربون بناء على اختيارك لنوع العربون",
        "packages_status"    => "حالة الباقة لحساب شركة",
        "packages_percentage"    => "نسبة العمولة لحساب الشركة",
        "file_upload"    => "الملف",
        "tax_region_id"    => "اسم المنطقة الجغرافية",
        "region_id"    => "اسم المنطقة الجغرافية",
        "tax_price_id"    => "سعر الضريبة",
        "country_id"    => "الدولة",
        "city_id"    => "المدينة",
        "sale_type"    => "نوع الاعلان",
        "main_pic"    => "الصورة الرئيسية",
        "check_center_status"    => "فحص المنتج",
        "deposit_price_status"    => "دفع عربون",
        "conversation_status"    => "تقبل التفاوض",
        "offer_price_status"    => "تقبل عرض سعر",
        "retrieval_type"    => "خيارات الاسترجاع",
        "retrieval_duration"    => "يجب على المشتري الاتصال بك في غضون",
        "shipping_method"    => "إعادة الشحن",
        "shipping_methods"    => "طرق الشحن",
        "tax_country_id"    => "ضريبة المبيعات",
        "tax_price"    => "قيمة الضريبة",
        "check"    => "الموافقة على الشروط والاحكام",
        "end_order_information"    => "تعليمات انهاء الطلب",
        "pics"    => "الصور",
        "payment" => "طريقة الدفع",
        "offer_amount" => "قيمة العرض للمنتج الواحد",
        "offer_number" => "الكمية",
        "mazad_price" => "سعر المزاد",
        "package_check_center_id" => "باقات مراكز الفحص",
        "status" => "الحالة",
        "start_date" => "تاريخ البداية",
        "end_date" => "تاريخ الانتهاء",
        "post_number" => "الرقم البريدي",
        "default_address" => "العنوان الافتراضى",
        "balance" => "المبلغ",
        "bank" => "اسم البنك",
        "iban" => "رقم الايبان",
        "moyasar_Key‬" => "Publishable Key",
        "whatsapp"=>"واتس اب",

        'center_name' => 'اسم المركز',

        'commercial_image1' => 'صورة السجل',
        'owner_name' => 'اسم المالك',
        'owner_number' => 'رقم الجوال',
        'responsible_center' => 'المسؤول عن المركز',// 1مالك 2 غيره
        'responsible_name' => 'اسم المسؤول',// owner or other
        'responsible_number' => 'رقم جوال المسؤول',// owner or other
        'responsible_image1' => 'صورة من اثبات المسؤول',// owner or other
        'tafwed_type' => 'نزع التفويض',// 1 وكالة 2 مكتب عمل 3 مالك
        'email_confirmation' => 'تأكيد البريد الالكتروني',

        'center_image1' => 'صورة المركز',
        'center_number_bank' => 'رقم الحساب البنكي للمركز',
        'commercial_number_confirmation' => 'تأكيد رقم السجل التجاري',
        'service' => 'الخدمة الرئيسية',
        'subService' => 'الخدمات',
        'details' => 'التفاصيل',
        'current_password' => 'كلمة المرور القديمة',
        'new_password' => 'كلمة المرور الجديدة',

        'car_image'            => 'صورة السيارة',

        'device_token'          => 'توكن الجهاز',
        'device_type'           => 'نوع التوكن', // 1 for android 2 for ios

        'multi_place'=> 'اكثر من موقع', // 0 for no 1 for yes
        'places'=> 'الحي',
        'university_drive'=> 'توصيل جامعات', // 0 for no 1 for yes
        'employees_drive'=> 'توصيل موظفات', // 0 for no 1 for yes
        'driver_id' => 'السائق',
        'nationality_id' => 'الجنسية',
        'age_id' => 'العمر',
        'company_id' => 'الشركة المصنعة',
        'car_model_id' => 'الموديل',
        'city_mode_id' => 'سنةالموديل',
        'passenger_id' => 'عدد الركاب',
        'order_type' => ' نوع الطلب',
        'from_city_id' => ' من مدينة',
        'from_region_id' => ' من حي',
        'deliver_time' => ' نوع التوصيل',
        'from_time' => ' ميعاد الذهاب',
        'to_time' => ' ميعاد العودة',
        'to_school' => 'الى الجامعة',
        'to_region_id' => 'الى الحي',





    ],
];
