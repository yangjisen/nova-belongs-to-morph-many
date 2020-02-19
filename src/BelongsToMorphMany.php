<?php

namespace Yangjisen\NovaBelongsToMorphMany;

use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Http\Requests\NovaRequest;

class BelongsToMorphMany extends BelongsTo
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'nova-belongs-to-morph-many';


    /**
     * Resolve the field's value.
     *
     * @param  mixed  $resource
     * @param  string|null  $attribute
     * @return void
     */
    public function resolve($resource, $attribute = null)
    {
        $value = null;

        if ($resource->relationLoaded($this->attribute)) {
            $value = $resource->getRelation($this->attribute);
        }

        if (! $value) {
            $value = $resource->{$this->attribute}()->withoutGlobalScopes()->getResults();
        }

        if ($value) {
            $this->belongsToId = $value->getKey();

            $resource = new $this->resourceClass($value);

            $this->value = $this->formatDisplayValue($resource);

            $this->viewable = $this->viewable
                && $resource->authorizedToView(request());
        }
    }


    /**
     * Build an associatable query for the field.
     * Here is where we add the depends on value and filter results
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  bool  $withTrashed
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function buildAssociatableQuery(NovaRequest $request, $withTrashed = false)
    {
        $query = parent::buildAssociatableQuery($request, $withTrashed);

        if($request->has('dependsOnValue')) {
            $query->whereHas('groups', function ($query) use ($request) {
                $query->where($this->meta['relationKey'], $request->dependsOnValue);
            });
        } else if($request->has('current')) {
            $query->where('id', $request->current);
        } else if(!$request->has('editMode')) {
            $query->where('id', $request->input('current', 0));
        }

        return $query;
    }


    /**
     * @param $belongsTo
     * @param $relationKey
     * @return BelongsToMorphMany
     */
    public function dependsOn($belongsTo, $relationKey)
    {
        return $this->withMeta([
            'dependsOn' => $belongsTo, // 关联的模型
            'relationKey' => $relationKey, // 关联的模型中间键
        ]);
    }

}
