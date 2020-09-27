<?php

namespace App\Repositories;
use Carbon\Carbon;

use App\Model\Ticket;
use Illuminate\Support\Str;
use App\Entities\TicketEntity;
use App\Entities\TicketCollection;
use App\Factories\TicketFactoryInterface;
use App\Repositories\TicketRepositoryInterface;

use Illuminate\Foundation\Application as Application; //DI利用のため
class TicketRepository implements TicketRepositoryInterface
{
    protected $ticketEloquent;

    public function __construct(Application $app)
    {
        $this->ticketFactory = $app->make('TicketFactory');
        $this->ticketEloquent = new Ticket();
    }

    public function getTickets(?array $queryArray = null, bool $getFinished = false, bool $getDeleted = false): TicketCollection
    {
        $withArray = ['ticketDisplaySequence'];

        $eloquent = $this->ticketEloquent->with($withArray);
        if (!is_null($queryArray)) {
            foreach ($queryArray as $key => $value) {
                $eloquent = $eloquent->where($key,$value);
            }
        }
        if (!$getFinished) {
            $eloquent = $eloquent->where('status','!=', self::STATUS_FINISHED);
        }
        if (!$getDeleted) {
            $eloquent = $eloquent->where('status','!=', self::STATUS_DELETED);
        }

        // $records = $eloquent->orderBy('display_sequence')->get();
        $eloquent = $eloquent->join('ticket_display_sequences', 'ticket_display_sequences.ticket_id', '=', 'tickets.id')
            ->orderBy('ticket_display_sequences.sequence', 'asc');
        $records = $eloquent->get();

        $ticketCollection = collect();
        foreach ($records as $record) {
            $ticketCollection->push(
                $this->ticketFactory->rebuildFromEloquent($record)
            );
        }
        return new TicketCollection($ticketCollection);
    }

    public function getById(int $id, bool $getDeleted = false): ?TicketEntity
    {
        $eloquent = $this->ticketEloquent->where('id', $id);
        if (!$getDeleted) {
            $eloquent = $eloquent->where('status','!=', TicketEntity::STATUS_DELETED);
        }
        $record = $eloquent->first();
        if (is_null($record)) {
            return null;
        }
        return $this->ticketFactory->rebuildFromEloquent($record);
    }

    public function storeTicket(TicketEntity $ticket): ?TicketEntity
    {
        $request = $ticket->toArray();

        // insert時、idにはnullが入っているので、削除する
        if (is_null($request['id'])) {
            return $this->insert($request);
        }
        // update時はそのまま
        return $this->update($request);
    }

    // public function storeTicket(TicketEntity $ticket): TicketEntity
    // {
    //     $request = $ticket->toArray();
    //     foreach ($request as $key => $value) {
    //         $this->ticket->$key = $value;
    //     }
    //     $developerPaymentHead->update();
    //     return $this->getByPrimary($developerPaymentHead->id);

    //     $storeRecord = $this->ticketEloquent->fill(
    //     )->save();

    //     return
    // }

    /**
     * チケットを新規登録する
     * TicketModelのfillableで指定されたフィールドのみ更新される
     *
     * @param array $request
     * @return TicketEntity
     */
    private function insert(array $request): ?TicketEntity
    {
        $fillArray = [];
        foreach ($request as $key => $value) {
            $fillArray[Str::snake($key)] = $value;
        }
        $this->ticketEloquent->fill($fillArray)->save();

        return $this->getById($this->ticketEloquent->id, false);
    }

    private function update(array $request): ?TicketEntity
    {
        $fillArray = [];
        foreach ($request as $key => $value) {
            $fillArray[Str::snake($key)] = $value;
        }
        $this->ticketEloquent->find($fillArray['id'])->update($fillArray);

        return $this->getById($fillArray['id'], false);
    }

}
