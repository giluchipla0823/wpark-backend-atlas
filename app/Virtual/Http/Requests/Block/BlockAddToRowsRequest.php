<?php

namespace App\Virtual\Http\Requests\Block;

/**
 * @OA\Schema(
 *      title="Block add to rows Request",
 *      description="Block add to rows request body data",
 *      type="object",
 *      @OA\Xml(
 *         name="BlockAddToRowsRequest"
 *      ),
 *      required={"rows"}
 * )
 */
class BlockAddToRowsRequest
{
    /**
     * @OA\Property(
     *     property="rows",
     *     type="array",
     *     @OA\Items(
     *          type="integer",
     *     ),
     *     @OA\Schema(type="array"),
     *     description="Ids de filas",
     *     example="[1, 2, 3]"
     * )
     */
    public $rows;
}
