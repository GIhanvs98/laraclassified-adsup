<?php

/*

    location : l={"d":1, "c":2}

    membership : m=1

    price : p={"min":100, "max":2300}

    category : c={id:1, "subId":45, fields:{f1:2,f3:4,f5:[1,2,3]}}

    fields : ... fields:{f1:2,f3:4,f5:[1,2,3]} ...}

            {
                f1:2,
                f3:4,
                f5:[1,2,3]
            }

            object = {}, array = []

    Eg:- http://127.0.0.1:8000/search?l=2&c={i:1,f:{f1:2,f3:4,f5:[1,2,3]}}&m=4

    here before the form send the get request, it will convert to a json object or an array.

    --------------------------------------------------------------------------

    Result :
                Location - 2
                Membership - 4
                Category - 1
                Fields - {f1:2,f3:4,f5:[1,2,3]}

    --------------------------------------------------------------------------

*/

namespace App\Http\Controllers\Web\Public;

use App\Models\Category;
use App\Models\City;
use App\Models\Membership;
use App\Models\Post;
use App\Models\User;
use App\Models\Field;
use App\Models\Transaction;
use App\Models\SubAdmin2;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class SearchController extends FrontController
{
    public $districts;

    public $query;

    public $location;

    public $sort;

    public $membership;

    public $category;

    public $categories;

    public $subCategories;

    // fields with the values from the query.
    public array $fields;

    // Load the fields from the db.
    public $adFields = null;

    public $price;

    public $topResults;

    public $results;

    public function loadLocations()
    {
        if (isset($this->location->d) && !empty($this->location->d)) {
            // If a district is selected
            $this->districts = SubAdmin2::where('code', $this->location->d)->where('active', 1)->withWhereHas('cities', function ($query) {
                $query->orderBy('order', 'asc'); })->first();
        } elseif (isset($this->location->c) && !empty($this->location->c)) {
            // If a city is selected
            $this->districts = City::whereId($this->location->c)->where('active', 1)->first()->subAdmin2()->withWhereHas('cities', function ($query) {
                $query->orderBy('order', 'asc'); })->first();
        } else {
            $this->districts = SubAdmin2::where('active', 1)->withWhereHas('cities', function ($query) {
                $query->orderBy('order', 'asc'); })->orderBy('order', 'asc')->get();
        }
    }

    public function loadCategories()
    {
        if (isset($this->category) && isset($this->category->id) && !empty($this->category->id)) {
            // $this->fields = Category::whereId($this->category->id)->where('active', 1)->first()->fields()->where('belongs_to', 'posts')->where('active', 1)->with('options')->orderBy('name')->get();

            $this->categories = Category::whereId($this->category->id)->where('active', 1)->with('fields')->orderBy('lft', 'asc')->first();

            $this->subCategories = Category::where('parent_id', $this->category->id)->with('fields')->where('active', 1)->orderBy('lft', 'asc')->get();

        } else {
            $this->categories = Category::where('active', 1)->whereNull('parent_id')->with('fields')->orderBy('lft', 'asc')->get();
        }
    }

    public function searchResults()
    {
        $this->results = Post::with(['pictures', 'currency', 'category', 'city', 'package', 'transactions']);

        // Filter according to search text.
        if (isset($this->query)) {
            $this->results->where(function (Builder $query) {
                $query->Where('title', 'like', '%' . $this->query . '%')
                    ->orWhere('description', 'like', '%' . $this->query . '%')
                    ->orWhere('tags', 'like', '%' . $this->query . '%')
                    ->orWhere('price', 'like', '%' . $this->query . '%');
            });
        }

        // Filter according to the location
        if (isset($this->location)) {
            if (isset($this->location->d) && !empty($this->location->d)) {
                // If a district is selected

                $cities = SubAdmin2::where('code', $this->location->d)->where('active', 1)->first()->cities()->pluck('id');

                $this->results->whereIn('city_id', $cities);

            } elseif (isset($this->location->c) && !empty($this->location->c)) {
                // If a city is selected

                $this->results->where('city_id', $this->location->c);
            } else {
                // Ignore.
            }
        }

        // Filter according to the membership
        if (isset($this->membership)) {
            if ($this->membership == '1') {
                // Ads by registered users.

                //$members = User::has('membership')->pluck('id');

                $this->results->whereHas('user', function ($query) {

                    $query->whereHas('membership', function ($query) {
                        $query->whereNot(function (Builder $query) {
                            $query->whereId(1)->orWhere('name', 'Non Member');
                        });
                    });
                });

                //$this->results->whereIn('user_id', $members);
            } else {
                // Ignore.
            }
        }

        // Filter according to price.
        if (isset($this->price)) {

            if (isset($this->price->max) && !empty($this->price->max) && isset($this->price->min) && !empty($this->price->min)) {

                $this->results->whereBetween('price', [$this->price->min, $this->price->max]);
            } elseif (isset($this->price->min) && !empty($this->price->min)) {

                $this->results->where('price', '>=', $this->price->min);
            } elseif (isset($this->price->max) && !empty($this->price->max)) {

                $this->results->where('price', '<=', $this->price->max);
            } else {
                // Ignore.
            }
        }

        // Filter according to category.
        if (isset($this->category)) {

            if (isset($this->category->id) && !empty($this->category->id) && isset($this->category->subId) && !empty($this->category->subId)) {
                // Sub/child category selected.

                $this->results->where('category_id', $this->category->subId);

                // Filter according to fields.
                if (isset($this->category->fields)) {

                    $thisCategoryFields = $this->category->fields;

                    $fields = Field::whereIn('id', array_keys($thisCategoryFields))->get();

                    foreach ($fields as $key => $field) {

                        switch ($field->type) {

                            case 'select':
                                // Select and options field.

                                if (isset($thisCategoryFields[$field->id]) && !empty($thisCategoryFields[$field->id])) {

                                    $this->results->whereHas('postValues', function ($query) use ($field, $thisCategoryFields) {

                                        $query->where('field_id', $field->id);
                                        $query->where('value', $thisCategoryFields[$field->id]);
                                        $query->orWhere('option_id', $thisCategoryFields[$field->id]);

                                        $query->with([
                                            'field' => function ($query) {

                                                $query->where('is_search_item_visible', 1);

                                                $query->whereHas('unit', function ($query) { });

                                            }
                                        ]);
                                    });
                                }
                                break;

                            case 'radio':
                                // Radio field.

                                if (isset($thisCategoryFields[$field->id]) && !empty($thisCategoryFields[$field->id])) {
                                    $this->results->whereHas('postValues', function ($query) use ($field, $thisCategoryFields) {

                                        $query->where('field_id', $field->id);
                                        $query->where('value', $thisCategoryFields[$field->id]);
                                        $query->orWhere('option_id', $thisCategoryFields[$field->id]);

                                        $query->with([
                                            'field' => function ($query) {

                                                $query->where('is_search_item_visible', 1);

                                                $query->whereHas('unit', function ($query) { });

                                            }
                                        ]);
                                    });
                                }
                                break;

                            case 'checkbox_multiple':
                                // Checkbox mutiple field.

                                if (isset($thisCategoryFields[$field->id]) && !empty($thisCategoryFields[$field->id])) {
                                    $this->results->whereHas('postValues', function ($query) use ($field, $thisCategoryFields) {

                                        $query->whereIn('option_id', $thisCategoryFields[$field->id]);

                                        $query->with([
                                            'field' => function ($query) {

                                                $query->where('is_search_item_visible', 1);

                                                $query->whereHas('unit', function ($query) { });

                                            }
                                        ]);
                                    });
                                }
                                break;

                            default:

                                // loading special search item fields.

                                $this->results->with([
                                    'postValues' => function ($query) {

                                        $query->whereNotNull('value');

                                        $query->with([
                                            'field' => function ($query) {

                                                $query->where('is_search_item_visible', 1);

                                                $query->whereHas('unit', function ($query) { });

                                            }
                                        ]);

                                    }
                                ]);

                                break;
                        }

                    }
                }
            } elseif (isset($this->category->id) && !empty($this->category->id)) {

                // Main category selected with or without children selected.

                $this->results->whereHas('category', function ($query) {

                    $query->whereHas('parent', function ($query) {
                        $query->whereId($this->category->id);
                    });

                    $query->orWhere('id', $this->category->id);
                });
            } else {
                return abort(404);
            }
        }


        // Filter according sort option.
        if (isset($this->sort)) {
            if ($this->sort == 'date_new_top') {
                // Sort according to .

                $this->results->orderBy('bumped_at', 'desc');
            } elseif ($this->sort == 'date_old_top') {
                // Sort according to .

                $this->results->orderBy('bumped_at', 'asc');
            } elseif ($this->sort == 'price_high_to_low') {
                // Sort according to .

                $this->results->orderBy('price', 'desc');
            } elseif ($this->sort == 'price_low_to_high') {
                // Sort according to .

                $this->results->orderBy('price', 'asc');
            } else {
                // Sort according to .

                $this->results->orderBy('bumped_at', 'desc');
            }
        } else {

            $this->results->orderBy('bumped_at', 'desc');
        }

        // $this->results->where('is_approved', 1)->paginate(20);

        $this->results = $this->results->whereNotNull('email_verified_at')->whereDate('email_verified_at', '<=', now())
            ->whereNotNull('phone_verified_at')->whereDate('phone_verified_at', '<=', now())
            ->whereNotNull('reviewed_at')->whereDate('reviewed_at', '<=', now())
            ->whereNull('archived_at')->orWhereDate('archived_at', '<=', now())
            ->whereNull('archived_manually_at')->orWhereDate('archived_manually_at', '<=', now())
            ->whereNull('deleted_at')->orWhereDate('deleted_at', '<=', now())
            ->paginate(20);

    }

    public function topResults()
    {

        $this->topResults = Transaction::valid('ad-promotion');

        $this->topResults = $this->topResults->whereHas('post', function ($query) {

            // $query->where('is_approved', 1);
            $query->whereNotNull('reviewed_at');
            $query->with([
                'pictures',
                'currency',
                'category',
                'city',
                'package',
                'postValues' => function ($query) {

                    // loading special search item fields.
    
                    $query->whereNotNull('value');

                    $query->with([
                        'field' => function ($query) {

                            $query->where('is_search_item_visible', 1);

                            $query->whereHas('unit', function ($query) { });

                        }
                    ]);

                }
            ]);

        })->with('post');

        $this->topResults = $this->topResults->whereHas('package', function ($query) {

            $query->where('packge_type', 'Top ads');
            $query->where('active', 1);
        });

        $this->topResults = $this->topResults->inRandomOrder()->take(2)->get();
    }

    public function loadFields()
    {

        if (isset($this->category->id)) {

            $this->adFields = Field::whereHas('categoryFields', function ($query) {

                $query->whereNull('parent_id');

                $query->where(function (Builder $query) {

                    $query->where('category_id', $this->category->id);

                    if (isset($this->category->subId)) {

                        $query->orWhere('category_id', $this->category->subId);
                    }

                    $query->orderBy('lft', 'asc');

                });

            })->where('belongs_to', 'posts')->where('active', 1)->with('options')->get();

        }
    }

    public function search(Request $request)
    {

        try {
            // Process values

            $this->query = $request->query('q');

            $this->sort = $request->query('sort');

            $this->membership = $request->query('m');

            $this->location = json_decode(base64_decode($request->query('l')));

            $this->category = json_decode(base64_decode($request->query('c')));

            $this->price = json_decode(base64_decode($request->query('p')));

            // Loading locations data.
            $this->loadLocations();

            if (!isset($this->districts)) {
                return abort(404);
            }

            // Loading membership data.
            if (!isset($this->membership)) {
                // Ignore
            } elseif ($this->membership === '1' || $this->membership === '0') {
                // Ignore
            } else {
                return abort(404);
            }

            // Loading categories.
            $this->loadCategories();

            // Loading Top Results/Top Ads
            $this->topResults();

            // Loading search results.
            $this->searchResults();

            // Loading fields.
            $this->loadFields();

            // Prevalidation of data is required.
            return view('pages.search', [
                'query' => $this->query,
                'location' => $this->location,
                'sort' => $this->sort,
                'membership' => $this->membership,
                'category' => $this->category,
                'categories' => $this->categories,
                'subCategories' => $this->subCategories,
                'adFields' => $this->adFields,
                'price' => $this->price,
                'districts' => $this->districts,
                'topResults' => $this->topResults,
                'results' => $this->results,
            ]);
        } catch (\Throwable $th) {
            throw $th;

            //return abort(404);
        }
    }
}
