<?php
/*
 * LaraClassifier - Classified Ads Web Application
 * Copyright (c) BeDigit. All Rights Reserved
 *
 * Website: https://laraclassifier.com
 * Author: BeDigit | https://bedigit.com
 *
 * LICENSE
 * -------
 * This software is furnished under a license and may be used and copied
 * only in accordance with the terms of such license and with the inclusion
 * of the above copyright notice. If you Purchased from CodeCanyon,
 * Please read the full License from here - https://codecanyon.net/licenses/standard
 */

namespace App\Models;

use App\Models\Scopes\LocalizedScope;
use App\Models\Traits\CountryTrait;
use App\Observers\SubAdmin1Observer;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Http\Controllers\Web\Admin\Panel\Library\Traits\Models\Crud;
use App\Http\Controllers\Web\Admin\Panel\Library\Traits\Models\SpatieTranslatable\HasTranslations;

class SubAdmin1 extends BaseModel
{
    use Crud, HasFactory, CountryTrait, HasTranslations;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'subadmin1';
    
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'code';
	
	/**
	 * The "type" of the primary key ID.
	 *
	 * @var string
	 */
	protected $keyType = 'string';
	
    public $incrementing = false;
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var boolean
     */
    public $timestamps = false;
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['country_code', 'code', 'name', 'active'];
	public $translatable = ['name'];
    
    /**
     * The attributes that should be hidden for arrays
     *
     * @var array
     */
    // protected $hidden = [];
    
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    // protected $casts = [];
    
    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    protected static function boot()
    {
        parent::boot();
	
		SubAdmin1::observe(SubAdmin1Observer::class);
		
		static::addGlobalScope(new LocalizedScope());
    }
    
    public function getNameHtml(): string
	{
        $currentUrl = preg_replace('#/(search)$#', '', url()->current());
        $editUrl = $currentUrl . '/' . $this->code . '/edit';
	
		return '<a href="' . $editUrl . '">' . $this->name . '</a>';
    }
	
	public function adminDivisions2Btn($xPanel = false): string
	{
		$url = admin_url('admins1/' . $this->code . '/admins2');
		
		$msg = trans('admin.Admin Divisions 2 of admin1', ['admin_division1' => $this->name]);
		$toolTip = ' data-bs-toggle="tooltip" title="' . $msg . '"';
		
		$out = '<a class="btn btn-xs btn-light" href="' . $url . '"' . $toolTip . '>';
		$out .= '<i class="fa fa-eye"></i> ';
		$out .= mb_ucfirst(trans('admin.admin divisions 2'));
		$out .= '</a>';
		
		return $out;
	}
	
	public function citiesBtn($xPanel = false): string
	{
		$url = admin_url('admins1/' . $this->code . '/cities');
		
		$msg = trans('admin.Cities of admin1', ['admin_division1' => $this->name]);
		$toolTip = ' data-bs-toggle="tooltip" title="' . $msg . '"';
		
		$out = '<a class="btn btn-xs btn-light" href="' . $url . '"' . $toolTip . '>';
		$out .= '<i class="fa fa-eye"></i> ';
		$out .= mb_ucfirst(trans('admin.cities'));
		$out .= '</a>';
		
		return $out;
	}
    
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    
    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */
    
    /*
    |--------------------------------------------------------------------------
    | ACCESSORS | MUTATORS
    |--------------------------------------------------------------------------
    */
	protected function id(): Attribute
    {
		return Attribute::make(
			get: fn ($value) => $this->attributes['code'],
		);
    }
	
	protected function name(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				if (isset($this->attributes['name']) && !isJson($this->attributes['name'])) {
					return $this->attributes['name'];
				}
				
				return $value;
			},
		);
	}
	
	/*
	|--------------------------------------------------------------------------
	| OTHER PRIVATE METHODS
	|--------------------------------------------------------------------------
	*/
}
