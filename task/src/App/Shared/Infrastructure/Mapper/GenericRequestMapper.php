<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Mapper;

use App\Shared\Infrastructure\Mapper\Attribute\MapField;
use App\Shared\Infrastructure\Mapper\Attribute\MapFrom;
use App\Shared\Infrastructure\Mapper\Transformer\TransformerInterface;
use Illuminate\Http\Request;
use ReflectionClass;
use ReflectionProperty;
use RuntimeException;

final class GenericRequestMapper
{
    public function map(Request $request, string $targetClass): object
    {
        $reflection = new ReflectionClass($targetClass);
        $instance = $reflection->newInstance();

        // Check for class-level source definition (e.g. MapFrom(Request::class))
        // This is primarily for documentation/validation if we wanted to enforce type checks
        // $classAttributes = $reflection->getAttributes(MapFrom::class);

        foreach ($reflection->getProperties() as $property) {
            $this->mapProperty($request, $property, $instance);
        }

        return $instance;
    }

    private function mapProperty(Request $request, ReflectionProperty $property, object $instance): void
    {
        $attributes = $property->getAttributes(MapField::class);
        if (empty($attributes)) {
            return;
        }

        /** @var MapField $mapField */
        $mapField = $attributes[0]->newInstance();

        // 1. Determine Key (Auto-map if null)
        $key = $mapField->key ?? $property->getName();

        // 2. Extract Value (Smart Lookup)
        $value = $this->extractValueSmart($request, $key);

        // 3. Transform Value
        if ($value !== null) {
            if ($mapField->transformer) {
                if (!class_exists($mapField->transformer) || !is_subclass_of($mapField->transformer, TransformerInterface::class)) {
                    throw new RuntimeException("Transformer {$mapField->transformer} must implement TransformerInterface");
                }
                /** @var TransformerInterface $transformer */
                $transformer = new ($mapField->transformer)();
                $value = $transformer->transform($value);
            } else {
                // Auto-instantiation logic for VOs if no transformer provided
                $type = $property->getType();
                if ($type && !$type->isBuiltin()) {
                    $typeName = $type->getName();
                    if (method_exists($typeName, 'fromString')) {
                        $value = $typeName::fromString((string) $value);
                    } elseif (method_exists($typeName, 'fromNullable')) {
                        $value = $typeName::fromNullable($value);
                    } elseif (enum_exists($typeName) && method_exists($typeName, 'tryFrom')) {
                        $value = $typeName::tryFrom($value);
                    }
                }
            }
        }

        // 4. Set Property
        if ($value !== null || ($property->getType()?->allowsNull())) {
            $property->setAccessible(true);
            $property->setValue($instance, $value);
        }
    }

    private function extractValueSmart(Request $request, string $key): mixed
    {
        // Priority 1: Route Parameter (e.g. {id})
        if ($request->route($key) !== null) {
            return $request->route($key);
        }

        // Priority 2: Input (Body/Query merged)
        // input() retrieves values from the request payload (JSON/Form) and query string
        $input = $request->input($key);
        if ($input !== null) {
            return $input;
        }

        // Priority 3: Custom Attributes (e.g. added by middleware)
        return $request->attributes->get($key);
    }
}
