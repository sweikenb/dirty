<?php

namespace Sweikenb\Library\Dirty\Service;

use Sweikenb\Library\Dirty\Api\ConfigInterface;
use Sweikenb\Library\Dirty\Factory;
use Sweikenb\Library\Dirty\Model\NormalizedModel;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class NormalizerService
{
    private readonly Factory $factory;
    private readonly SerializerInterface $serializer;

    public function __construct(?Factory $factory = null, ?SerializerInterface $serializer = null)
    {
        $this->factory = $factory ?? new Factory();
        $this->serializer = $serializer ?? new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
    }

    public function execute(string $id, array|object $toCheck, ?ConfigInterface $config = null): NormalizedModel
    {
        $configKey = $this->getConfigKey($config);
        $storageKey = is_object($toCheck)
            ? sprintf('%s:%s:%s', get_class($toCheck), $id, $configKey)
            : sprintf('array:%s:%s', $id, $configKey);

        $data = json_decode($this->serializer->serialize($toCheck, 'json'), true);

        $flatten = [];
        $this->flatten('', $data, $flatten);
        unset($data);

        if ($config) {
            $this->applyFieldConfig($config, $flatten);
        }

        ksort($flatten);

        return $this->factory->createNormalized($storageKey, $flatten, md5(serialize($flatten)));
    }

    public function getConfigKey(?ConfigInterface $config = null): string
    {
        return md5(serialize([
            $config?->getFieldsToCheck() ?? [],
            $config?->getFieldsToIgnore() ?? [],
        ]));
    }

    private function flatten(string $prev, array &$arr, array &$flatten): void
    {
        foreach ($arr as $key => $value) {
            if (is_array($value)) {
                $this->flatten($prev.$key.'.', $value, $flatten);
            } else {
                $flatten[$prev.$key] = (string) $value;
            }
            unset($arr[$key]);
        }
    }

    private function applyFieldConfig(ConfigInterface $config, array &$flatten): void
    {
        $hasCheckFields = !empty($config->getFieldsToCheck());
        $hasIgnoreFields = !empty($config->getFieldsToIgnore());
        if ($hasCheckFields || $hasIgnoreFields) {
            $flattenKeys = array_keys($flatten);

            if ($hasCheckFields) {
                $pattern = sprintf(
                    '/^(%s)/',
                    implode(
                        '|',
                        array_map(fn (string $str) => str_replace('\*', '(.+)', preg_quote($str, '/')), $config->getFieldsToCheck())
                    )
                );
                $keepKeys = [];
                foreach ($flattenKeys as $flattenKey) {
                    if (preg_match($pattern, $flattenKey)) {
                        $keepKeys[$flattenKey] = $flattenKey;
                    }
                }
            } else {
                $keepKeys = array_combine($flattenKeys, $flattenKeys);
            }

            if ($hasIgnoreFields) {
                $pattern = sprintf(
                    '/^(%s)/',
                    implode(
                        '|',
                        array_map(fn (string $str) => str_replace('\*', '(.+)', preg_quote($str, '/')), $config->getFieldsToIgnore())
                    )
                );
                foreach ($keepKeys as $flattenKey) {
                    if (preg_match($pattern, $flattenKey)) {
                        unset($keepKeys[$flattenKey]);
                    }
                }
            }

            $flatten = array_filter(
                $flatten,
                fn ($key) => isset($keepKeys[$key]),
                ARRAY_FILTER_USE_KEY
            );
        }
    }
}
