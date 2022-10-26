<?php
/**
 * Created by PhpStorm.
 * User: kemal
 * Date: 06.09.18
 * Time: 14:49
 */

namespace Inensus\Ticket\Http\Controllers;


use Illuminate\Http\Request;
use Inensus\Ticket\Http\Requests\TicketCategoryRequest;
use Inensus\Ticket\Http\Resources\TicketResource;
use Inensus\Ticket\Models\TicketCategory;
use Inensus\Ticket\Services\TicketCategoryService;

class TicketCategoryController extends Controller
{

    public function __construct(private TicketCategoryService $ticketCategoryService)
    {
    }

    /**
     * A list of all stored labels/categories
     */
    public function index(Request $request): TicketResource
    {
        $limit = $request->input('limit');
        $outsource = $request->get('outsource');
        return  TicketResource::make($this->ticketCategoryService->getAll($limit, $outsource));
    }


    public function store(TicketCategoryRequest $request): TicketResource
    {
        $ticketCategoryData = [
            'label_name' => $request->input('labelName'),
            'label_color' => $request->input('labelColor'),
            'out_source' => $request->input('outSourcing')
        ];

        return  TicketResource::make($this->ticketCategoryService->create($ticketCategoryData));
    }
}
