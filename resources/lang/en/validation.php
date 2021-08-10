<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => ':atribut harus diterima.',
    'active_url' => ':atribut bukan URL yang valid.',
    'after' => ':atribut harus berupa tanggal setelah :date.',
    'after_or_equal' => ':atribut harus berupa tanggal setelah atau sama dengan :date.',
    'alpha' => ':atribut hanya boleh berisi huruf.',
    'alpha_dash' => ':atribut hanya boleh berisi huruf, angka, tanda hubung, dan garis bawah.',
    'alpha_num' => ':atribut hanya boleh berisi huruf dan angka.',
    'array' => ':atribut harus berupa larik.',
    'before' => ':atribut harus berupa tanggal sebelum :date.',
    'before_or_equal' => ':atribut harus berupa tanggal sebelum atau sama dengan :date.',
    'between' => [
        'numeric' => ':atribut harus antara:min and :max.',
        'file' => ':atribut harus antara :min and :max kilobytes.',
        'string' => ':atribut harus antara :min and :max characters.',
        'array' => ':atribut harus antara :min and :max items.',
    ],
    'boolean' => ':bidang atribut harus benar atau salah.',
    'confirmed' => ':konfirmasi atribut tidak cocok.

',
    'date' => ':atribut bukan tanggal yang valid.',
    'date_equals' => ':atribut harus berupa tanggal yang sama dengan :date.',
    'date_format' => ':atribut tidak cocok dengan format :format.',
    'different' => ':atribut dan :other harus berbeda.',
    'digits' => ':atribut harus :digits digits.',
    'digits_between' => ':atribut harus antara :min and :max digits.',
    'dimensions' => ':atribut memiliki dimensi gambar yang tidak valid.',
    'distinct' => ':bidang atribut memiliki nilai duplikat.',
    'email' => ':atribut harus berupa alamat email yang valid.',
    'ends_with' => ':atribut harus diakhiri dengan salah satu dari berikut ini: :values.',
    'exists' => 'yang dipilih :attribute is invalid.',
    'file' => ':atribut harus berupa file.',
    'filled' => ':bidang atribut harus memiliki nilai.',
    'gt' => [
        'numeric' => ':atribut harus lebih besar dari :value.',
        'file' => ':atribut harus lebih besar dari :value kilobytes.',
        'string' => ':atribut harus lebih besar dari :value characters.',
        'array' => ':atribut harus memiliki lebih dari :value items.',
    ],
    'gte' => [
        'numeric' => ':atribut harus lebih besar dari atau sama :value.',
        'file' => ':atribut harus lebih besar dari atau sama :value kilobytes.',
        'string' => ':atribut harus lebih besar dari atau sama :value characters.',
        'array' => ':atribut yang harus dimiliki :value items or more.',
    ],
    'image' => ':atribut harus berupa gambar.',
    'in' => 'selected :atribut tidak valid.',
    'in_array' => ':bidang atribut tidak ada di :other.',
    'integer' => ':atribut harus berupa bilangan bulat.',
    'ip' => ':atribut harus berupa alamat IP yang valid.',
    'ipv4' => ':atribut harus berupa alamat IPv4 yang valid.

',
    'ipv6' => ':atribut harus berupa alamat IPv6 yang valid.',
    'json' => ':atribut harus berupa string JSON yang valid.',
    'lt' => [
        'numeric' => ':atribut harus lebih kecil dari :value.',
        'file' => ':atribut harus lebih kecil dari :value kilobytes.',
        'string' => ':atribut harus lebih kecil dari :value characters.',
        'array' => ':atribut harus memiliki kurang dari :value items.',
    ],
    'lte' => [
        'numeric' => ':atribut harus kurang dari atau sama :value.',
        'file' => ':atribut harus kurang dari atau sama :value kilobytes.',
        'string' => ':atribut harus kurang dari atau sama :value characters.',
        'array' => ':atribut tidak boleh memiliki lebih dari :value items.',
    ],
    'max' => [
        'numeric' => ':atribut tidak boleh lebih besar dari :max.',
        'file' => ':atribut tidak boleh lebih besar dari :max kilobytes.',
        'string' => ':atribut tidak boleh lebih besar dari :max characters.',
        'array' => ':atribut tidak boleh memiliki lebih dari :max items.',
    ],
    'mimes' => ':atribut harus berupa file bertipe: :values.',
    'mimetypes' => ':atribut harus berupa file bertipe: :values.',
    'min' => [
        'numeric' => ':atribut harus setidaknya :min.',
        'file' => ':atribut harus setidaknya :min kilobytes.',
        'string' => ':atribut harus setidaknya :min characters.',
        'array' => ':atribut harus memiliki setidaknya :min items.',
    ],
    'multiple_of' => ':atribut harus kelipatan dari :value.',
    'not_in' => 'yang dipilih :attribute is invalid.',
    'not_regex' => ':format atribut tidak valid.',
    'numeric' => ':atribut harus berupa angka.',
    'password' => 'Kata sandi salah.',
    'present' => ':bidang atribut harus ada.',
    'regex' => ':format atribut tidak valid.',
    'required' => ':bidang atribut wajib diisi.',
    'required_if' => ':bidang atribut diperlukan ketika :other is :value.',
    'required_unless' => ':bidang atribut wajib diisi kecuali :other is in :values.',
    'required_with' => ':bidang penghargaan diperlukan saat :values is present.',
    'required_with_all' => ':bidang atribut diperlukan ketika :values are present.',
    'required_without' => ':bidang atribut diperlukan ketika :values is not present.',
    'required_without_all' => ':bidang atribut wajib diisi jika tidak ada :values are present.',
    'prohibited' => ':bidang atribut dilarang.',
    'prohibited_if' => ':bidang atribut dilarang ketika :other is :value.',
    'prohibited_unless' => ':bidang atribut dilarang kecuali :other is in :values.',
    'same' => ':atribut dan :other must match.',
    'size' => [
        'numeric' => ':atribut harus :size.',
        'file' => ':atribut harus :size kilobytes.',
        'string' => ':attribute must be :size characters.',
        'array' => ':atribut harus mengandung:size items.',
    ],
    'starts_with' => ':atribut harus dimulai dengan salah satu dari berikut ini: :values.',
    'string' => ':atribut harus berupa string.',
    'timezone' => ':atribut harus berupa zona yang valid.',
    'unique' => ':atribut sudah diambil.',
    'uploaded' => ':atribut gagal diunggah.',
    'url' => ':format atribut tidak valid.',
    'uuid' => ':atribut harus berupa UUID yang valid.',

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
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
