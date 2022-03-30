<?php

namespace App\Virtual\Http\Requests\Slot;

/**
 * @OA\Schema(
 *      title="Slot Update Request",
 *      description="Slot Update request body data",
 *      type="object",
 *      @OA\Xml(
 *         name="SlotUpdateRequest"
 *      ),
 * )
 */
class SlotUpdateRequest
{
    /**
     * @OA\Property(
     *     property="comments",
     *     type="string",
     *     description="Comentarios sobre el slot",
     *     example="La slot está reservado"
     * )
     */
    public $comments;
}
