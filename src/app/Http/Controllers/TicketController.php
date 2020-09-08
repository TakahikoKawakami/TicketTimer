<?php

namespace App\Http\Controllers;

use App\Utils\ArrayUtil;
use App\Services\TicketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Factories\TicketFactoryInterface;
use App\Http\Requests\Ticket\GetTicketRequest;
use App\Http\Requests\Ticket\CreateTicketRequest;
use App\Http\Requests\Ticket\UpdateTicketRequest;
use App\Repositories\TicketRepositoryInterface;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class TicketController extends Controller
{
    protected $ticketFactory;
    protected $ticketRepository;
    protected $ticketService;

    public function __construct(TicketFactoryInterface $ticketFactory, TicketRepositoryInterface $ticketRepository)
    {
        // $this->middleware('auth');
        $this->ticketService = new TicketService(
            $ticketFactory,
            $ticketRepository
        );
        $this->ticketFactory = $ticketFactory;
        $this->ticketRepository = $ticketRepository;
    }

    public function index()
    {
        $tickets = $this->ticketService->getTickets();
        return view('index-bootstrap')->with(["tickets"=>$tickets]);
    }

    public function apiIndex(GetTicketRequest $request): JsonResponse
    {
        $user = auth()->user();
        $tickets = $this->ticketService->getTickets($request->all());
        $ticketArray = $tickets->toArray();
        $tickets = ArrayUtil::toSnakeKeys($ticketArray);

        return response()->json($tickets);
    }

    public function apiCreate(CreateTicketRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $newTicketEntity = $this->ticketService->createTicket($request->all());
            $returnTicket = ArrayUtil::toSnakeKeys($newTicketEntity->toArray());
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('error');
            throw $e;
        }
        return response()->json($returnTicket);
    }

    public function apiUpdate(UpdateTicketRequest $request, int $id)
    {
        try {
            DB::beginTransaction();
            $newTicketEntity = $this->ticketService->updateTicket($id, $request->all());
            $returnTicket = ArrayUtil::toSnakeKeys($newTicketEntity->toArray());
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('error');
            throw $e;
        }
        // return response()->json($returnTicket);
        return $request;
    }

    /**
     * CollectionとPaginatorからレスポンスを生成
     *
     * @param EloquentCollection $items
     * @param integer $countFullCollection
     * @param LengthAwarePaginator $paginator
     * @return Response
     */
    // private function setJsonResponseWithPaginator(EloquentCollection $items, int $countFullCollection, LengthAwarePaginator $paginator): Response
    // {
    //     $itemsArray = ArrayUtil::toCamelKeys($items->toArray());
    //     $res = response(json_encode( $itemsArray , JSON_UNESCAPED_UNICODE), 200)
    //             ->header('Content-Type', 'application/json')
    //             ->header('Total', $paginator->total())
    //             ->header('Current-Page', $paginator->currentPage())
    //             ->header('Per-Page', $paginator->perPage());
    //     if($countFullCollection > 0) {
    //         $res -> header('Link', "<{$paginator->previousPageUrl()}>; rel=\"prev\",<{$paginator->nextPageUrl()}>; rel=\"next\",");
    //     }
    //     return $res;
    // }
}
