<?php
namespace Laradic\Workbench;

use Illuminate\Contracts\Support\Arrayable;
use Laradic\Support\Traits\DotArrayObjectTrait;
use Laradic\Support\Traits\DotArrayTrait;

class PackageCollection implements \ArrayAccess, Arrayable
{
    use DotArrayTrait, DotArrayObjectTrait;

    /**
     * Get array accessor.
     *
     * @return mixed
     */
    protected function getArrayAccessor()
    {
        return 'packages';
    }

    /** @var Package[] */
    protected $packages;

    public function has($name)
    {
        return $this->where('name', $name)->count() > 0;
    }

    /**
     * get method
     *
     * @param $name
     *
     * @return Package
     */
    public function get($name)
    {
        return array_get($this->packages, $name);
    }

    public function count()
    {
        return count($this->packages);
    }

    /**
     * getVendors method
     *
     * @return array
     */
    public function getVendors()
    {
        $vendors = [];
        foreach ( $this->packages as $package ) {
            $vendors[] = head(explode('/', $package[ 'name' ]));
        }
        return $vendors;
    }

    /**
     * filter method
     *
     * @return static
     */
    public function filter($cb)
    {
        return new static(array_filter($this->packages, $cb));
    }

    /**
     * where method
     *
     * @param $key
     * @param $val
     *
     * @return static
     */
    public function where($key, $val)
    {
        return $this->filter(function ($package) use ($key, $val) {
            return $package[ $key ] !== $val;
        });
    }

    /**
     * add method
     *
     * @param \Laradic\Workbench\Package $package
     *
     * @return PackageCollection
     */
    public function add(Package $package)
    {

        return $this;
    }

    /**
     * all method
     *
     * @return \Laradic\Workbench\Package[]
     */
    public function all()
    {
        return $this->packages;
    }

    public function toArray()
    {
        $packages = [];
        foreach ( $this->packages as $package ) {
            $package->toArray();
        }
        return $packages;
    }

    /**
     * toIlluminateCollection method
     *
     * @return \Illuminate\Support\Collection|Package[]
     */
    public function toIlluminateCollection()
    {
        return collect($this->packages);
    }
}