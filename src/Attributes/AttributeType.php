<?php

namespace Codex\Attributes;


use Codex\Support\Enum;

/**
 * @method static AttributeType STRING()
 * @method static AttributeType BOOL()
 * @method static AttributeType INT()
 * @method static AttributeType FLOAT()
 * @method static AttributeType MAP()
 * @method static AttributeType MIXED()
 */
class AttributeType extends Enum
{
    const STRING = 'string';
    const BOOL = 'bool';
    const INT = 'int';
    const FLOAT = 'float';
    const ARRAY = 'array';
    const MAP = 'map';
    const MIXED = 'mixed';
    const RECURSIVE = 'recursive';
    const RECURSE = 'recurse';

    public static $apiTypeMap = [
        self::STRING    => 'String',
        self::BOOL      => 'Boolean',
        self::INT       => 'Int',
        self::FLOAT     => 'Int',
        self::ARRAY     => '[Assoc]',
        self::MAP       => 'Assoc',
        self::MIXED     => 'Mixed',
        self::RECURSIVE => '[Assoc]',
        self::RECURSE   => '[Assoc]',
    ];

    public static $configNodeTypeMap = [
        self::STRING    => 'scalar',
        self::BOOL      => 'boolean',
        self::INT       => 'integer',
        self::FLOAT     => 'float',
        self::ARRAY     => 'array',
        self::MAP       => 'array',
        self::MIXED     => 'variable',
        self::RECURSIVE => 'recursive',
        self::RECURSE   => 'recurse',
    ];

    public function isChildType()
    {
        return $this->oneOf(self::MAP, self::ARRAY, self::RECURSE, self::RECURSIVE);
    }

    public function oneOf(...$types)
    {
        foreach ($types as $type) {
            if ($this->is($type)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param AttributeType|string $type
     *
     * @return mixed|string
     */
    public static function getConfigNodeTypeName($type)
    {
        if ($type instanceof static) {
            $type = $type->getValue();
        }
        return array_key_exists($type, static::$configNodeTypeMap) ? static::$configNodeTypeMap[ $type ] : 'variable';
    }

    /**
     * @param AttributeType|string $type
     *
     * @return mixed|string
     */
    public static function getApiType($type)
    {
        if(!$type instanceof static){
            $type = static::get($type);
            if($type->is(static::ARRAY)){

            }
        }
        if ($type instanceof static) {
            $type = $type->getValue();
        }
        return array_key_exists($type, static::$apiTypeMap) ? static::$apiTypeMap[ $type ] : 'Mixed';
    }

    public function toApiType()
    {
        return static::getApiType($this);
    }

    public function toConfigNodeTypeName()
    {
        return static::getConfigNodeTypeName($this);
    }


    final public static function ARRAY($childType)
    {
        if ( ! $childType instanceof static) {
            $childType = self::byName(strtoupper($childType));
        }
        $type = new static('array');
        $type->setChildType($childType);
        return $type;
    }

    /** @var static */
    protected $childType;

    public function getChildType()
    {
        return $this->childType;
    }

    public function setChildType($childType)
    {
        $this->childType = $childType;
        return $this;
    }


}

