<?php

namespace App\Traits;

use App\Models\Field;
use App\Models\FieldOption;
use App\Models\Membership;
use App\Models\Post;
use App\Models\PostValue;

trait PostAdTrait
{

    /**
     * Return the posts count of a guest or a registered user.
     * @param string $user
     * @param Membership $membership
     * @return int
     */
    protected function postsCount(string $user, $membership): int
    {
        $postsCount = 0;

        if ($user == "auth") {

            $user = auth()->user();

            if ($membership->allowed_ads_category_rate == "per_main_category") {

                $postsCount = Post::where('user_id', $user->id)->where('category_id', $this->parentCategory->id)->count();

            } elseif ($membership->allowed_ads_category_rate == "ignore") {

                $postsCount = Post::where('user_id', $user->id)->count();

            } else {

                $postsCount = Post::where('user_id', $user->id)->count();
            }

        } else if ($user == "guest") {

            $guestUser = session('guest-user');

            $posts = $guestUser->getPosts();

            if ($membership->allowed_ads_category_rate == "per_main_category") {

                $postsCount = Post::whereIn('id', $posts)->where('category_id', $this->parentCategory->id)->count();

            } elseif ($membership->allowed_ads_category_rate == "ignore") {

                $postsCount = Post::whereIn('id', $posts)->count();

            } else {

                $postsCount = Post::whereIn('id', $posts)->count();
            }

        }

        return $postsCount;
    }

    /**
     * Generate the ad title.
     * @return string
     */
    public function generateTitle(): string|null
    {
        $pattern = $this->category->title_auto_generation_fields_order;

        $titleFieldValue = $this->title;

        $fieldsWithValues = $this->fields;

        $title = $pattern;

        $checkbox_collector = ", ";

        if (isset($pattern)) {

            if (preg_match_all("/\<[0-9]*\>/i", $pattern, $fields)) {

                // Filtering starts...

                foreach ($fields as $key => $field) {

                    $fields[$key] = str_replace("<", "", $fields[$key]);

                    $fields[$key] = str_replace(">", "", $fields[$key]);

                }

                // Filtered field_id :- $fields[$key]

                foreach ($fields[0] as $key => $field) {

                    $fieldId = $fields[0][$key];

                    if (isset($fieldId) && Field::whereId($fieldId)->exists()) {

                        $db_field = Field::find($fieldId);

                        if ($db_field->type == "select" || $db_field->type == "radio" || $db_field->type == "checkbox") {

                            if (FieldOption::whereId($fieldsWithValues[$fieldId])->exists()) {

                                $value = FieldOption::find($fieldsWithValues[$fieldId])->value;

                                $title = str_replace("<$fieldId>", $value, $title);
                            }

                        } else if ($db_field->type == "checkbox_multiple") {

                            $valuesCombination = "";

                            if (isset($fieldsWithValues[$fieldId]) && !empty($fieldsWithValues[$fieldId])) {

                                foreach ($fieldsWithValues[$fieldId] as $optionId) {

                                    if (FieldOption::whereId($optionId)->exists()) {

                                        $value = FieldOption::find($optionId)->value;

                                        $valuesCombination .= $value . $checkbox_collector;
                                    }
                                }
                            }

                            $valuesCombination = substr_replace($valuesCombination, "", -2);

                            $title = str_replace("<$fieldId>", $valuesCombination, $title);

                        } else {

                            if (isset($fieldsWithValues[$fieldId]) && !empty($fieldsWithValues[$fieldId])) {

                                $title = str_replace("<$fieldId>", $fieldsWithValues[$fieldId], $title);
                            }
                        }

                    }

                }

                return $title;

            } else {

                return $titleFieldValue;
            }

        } else {

            return $titleFieldValue;
        }
    }

    /**
     * Save fields in the database.
     * @return void
     */
    public function saveFields(): void
    {
        $fields = $this->fields ?? [];
        $post = $this->post ?? null;

        foreach ($fields as $id => $adField) {

            $field = Field::find($id);

            if (
                isset($post) && isset($post->id) && !empty($post->id) &&
                isset($field) && isset($field->type) && !empty($field->type)
            ) {

                if ($field->type == "checkbox_multiple") {

                    foreach ($adField as $value) {

                        $PostValue = PostValue::where('post_id', $post->id)->where('field_id', $field->id)->where('option_id', $value)->first();

                        if (!$PostValue) {

                            $PostValue = new PostValue;

                            $PostValue->post()->associate($post);

                            $PostValue->field()->associate($field);
                        }

                        $PostValue->option_id = $value;

                        $PostValue->value = $value;

                        $PostValue->save();
                    }

                } else {

                    $PostValue = PostValue::where('post_id', $post->id)->where('field_id', $id)->first();

                    if (!$PostValue) {

                        $PostValue = new PostValue;

                        $PostValue->post()->associate($post);

                        $PostValue->field()->associate($field);
                    }

                    if ($field->type == "checkbox" || $field->type == "select") {

                        $PostValue->option_id = $adField;
                    }

                    $PostValue->value = $adField;

                    $PostValue->save();
                }
            }
        }

    }
}