<?php

function randomStr(int $length = 25): string
{
    $signs = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $max = mb_strlen($signs) - 1;

    $buffer = '';
    for ($i = 0; $i < $length; ++$i) {
        $buffer .= $signs[mt_rand(0, $max)];
    }

    return $buffer;
}

class User
{
    public static function create(): self
    {
        return new self(
            mt_rand(1000, 100000),
            randomStr(),
            sprintf('%s@%s.com', randomStr(15), randomStr(15)),
            randomStr(),
            randomStr(),
            SimpleVal::create(),
            SimpleVal::create(),
        );
    }

    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
        public readonly string $passwordSalt,
        public readonly SimpleVal $locationDetails,
        public readonly SimpleVal $preferences,
    ) {
    }
}

class SimpleVal
{
    public static function create(): self
    {
        return new self(
            mt_rand(1000, 100000),
            randomStr(),
            randomStr(),
            new DateTime('now')
        );
    }

    public function __construct(
        public readonly int $id,
        public readonly string $key,
        public readonly string $value,
        public readonly DateTime $date,
    ) {
    }
}

return function (int $numFields = 30) {
    $fields = [];
    for ($i = 0; $i < $numFields; ++$i) {
        $code = randomStr();
        $fields[$code] = [
            'code' => $code,
            'data' => SimpleVal::create(),
        ];
    }

    return [
        'id' => mt_rand(100, 10000),
        'owner' => User::create(),
        'slug' => randomStr(),
        'name' => randomStr(),
        'reference' => SimpleVal::create(),
        'tags' => [
            SimpleVal::create(),
            SimpleVal::create(),
        ],
        'categories' => [
            SimpleVal::create(),
            SimpleVal::create(),
        ],
        'fields' => $fields,
        'details' => randomStr(500),
        'createdAt' => new DateTime('-10 days'),
        'createdBy' => User::create(),
        'updatedAt' => new DateTime('now'),
        'updatedBy' => User::create(),
    ];
};
