<?php

return [

    'message' => 'エラー内容を確認してください。',

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

    'accepted' => ':attribute を承認する必要があります。',
    'accepted_if' => ':other が :value の場合、:attribute を承認する必要があります。',
    'active_url' => ':attribute は有効なURLである必要があります。',
    'after' => ':attribute は :date より後の日付である必要があります。',
    'after_or_equal' => ':attribute は :date 以降の日付である必要があります。',
    'alpha' => ':attribute は文字のみを含めることができます。',
    'alpha_dash' => ':attribute は文字、数字、ダッシュ、アンダースコアのみを含めることができます。',
    'alpha_num' => ':attribute は文字と数字のみを含めることができます。',
    'array' => ':attribute は配列である必要があります。',
    'ascii' => ':attribute は半角英数字および記号のみを含めることができます。',
    'before' => ':attribute は :date より前の日付である必要があります。',
    'before_or_equal' => ':attribute は :date 以前の日付である必要があります。',
    'between' => [
        'array' => ':attribute の項目数は :min から :max の間でなければなりません。',
        'file' => ':attribute のファイルサイズは :min から :max KBの間でなければなりません。',
        'numeric' => ':attribute は :min から :max の間の数値である必要があります。',
        'string' => ':attribute の文字数は :min から :max の間である必要があります。',
    ],
    'boolean' => ':attribute は true または false である必要があります。',
    'can' => ':attribute には許可されていない値が含まれています。',
    'confirmed' => ':attribute の確認が一致しません。',
    'contains' => ':attribute に必要な値が含まれていません。',
    'current_password' => 'パスワードが正しくありません。',
    'date' => ':attribute は有効な日付である必要があります。',
    'date_equals' => ':attribute は :date と同じ日付である必要があります。',
    'date_format' => ':attribute は :format 形式である必要があります。',
    'decimal' => ':attribute は小数点以下 :decimal 桁である必要があります。',
    'declined' => ':attribute を拒否する必要があります。',
    'declined_if' => ':other が :value の場合、:attribute を拒否する必要があります。',
    'different' => ':attribute と :other は異なる必要があります。',
    'digits' => ':attribute は :digits 桁である必要があります。',
    'digits_between' => ':attribute は :min から :max 桁の間である必要があります。',
    'dimensions' => ':attribute の画像サイズが無効です。',
    'distinct' => ':attribute には重複した値が含まれています。',
    'doesnt_end_with' => ':attribute は次のいずれかで終わってはいけません: :values。',
    'doesnt_start_with' => ':attribute は次のいずれかで始まってはいけません: :values。',
    'email' => ':attribute は有効なメールアドレスである必要があります。',
    'ends_with' => ':attribute は次のいずれかで終わる必要があります: :values。',
    'enum' => '選択された :attribute は無効です。',
    'exists' => '選択された :attribute は無効です。',
    'extensions' => ':attribute は次の拡張子のいずれかである必要があります: :values。',
    'file' => ':attribute はファイルである必要があります。',
    'filled' => ':attribute には値を入力する必要があります。',
    'gt' => [
        'array' => ':attribute の項目数は :value を超えている必要があります。',
        'file' => ':attribute のファイルサイズは :value KBを超えている必要があります。',
        'numeric' => ':attribute は :value を超える数値である必要があります。',
        'string' => ':attribute の文字数は :value を超える必要があります。',
    ],
    'gte' => [
        'array' => ':attribute の項目数は :value 以上である必要があります。',
        'file' => ':attribute のファイルサイズは :value KB以上である必要があります。',
        'numeric' => ':attribute は :value 以上の数値である必要があります。',
        'string' => ':attribute の文字数は :value 以上である必要があります。',
    ],
    'hex_color' => ':attribute は有効な16進数のカラーコードである必要があります。',
    'image' => ':attribute は画像である必要があります。',
    'in' => '選択された :attribute は無効です。',
    'in_array' => ':attribute は :other に存在する必要があります。',
    'integer' => ':attribute は整数である必要があります。',
    'ip' => ':attribute は有効なIPアドレスである必要があります。',
    'ipv4' => ':attribute は有効なIPv4アドレスである必要があります。',
    'ipv6' => ':attribute は有効なIPv6アドレスである必要があります。',
    'json' => ':attribute は有効なJSON文字列である必要があります。',
    'lowercase' => ':attribute は小文字である必要があります。',
    'lt' => [
        'array' => ':attribute の項目数は :value 未満である必要があります。',
        'file' => ':attribute のファイルサイズは :value KB未満である必要があります。',
        'numeric' => ':attribute は :value 未満の数値である必要があります。',
        'string' => ':attribute の文字数は :value 未満である必要があります。',
    ],
    'max' => [
        'array' => ':attribute の項目数は :max を超えてはいけません。',
        'file' => ':attribute のファイルサイズは :max KBを超えてはいけません。',
        'numeric' => ':attribute は :max を超えてはいけません。',
        'string' => ':attribute の文字数は :max を超えてはいけません。',
    ],
    'min' => [
        'array' => ':attribute の項目数は最低 :min 個必要です。',
        'file' => ':attribute のファイルサイズは最低 :min KB必要です。',
        'numeric' => ':attribute は最低 :min である必要があります。',
        'string' => ':attribute の文字数は最低 :min 文字必要です。',
    ],
    'numeric' => ':attribute は数値である必要があります。',
    'password' => [
        'letters' => ':attribute には少なくとも1つの文字を含める必要があります。',
        'mixed' => ':attribute には少なくとも1つの大文字と1つの小文字を含める必要があります。',
        'numbers' => ':attribute には少なくとも1つの数字を含める必要があります。',
        'symbols' => ':attribute には少なくとも1つの記号を含める必要があります。',
        'uncompromised' => '提供された :attribute はデータ漏洩で検出されました。別のものを選択してください。',
    ],
    'required' => ':attribute は必須項目です。',
    'unique' => ':attribute はすでに使用されています。',
    'uploaded' => ':attribute のアップロードに失敗しました。',
    'url' => ':attribute は有効なURLである必要があります。',
    'uuid' => ':attribute は有効なUUIDである必要があります。',

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
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'password' => 'パスワード',
    ],

];
