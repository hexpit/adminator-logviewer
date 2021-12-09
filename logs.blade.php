@extends('log-viewer::adminator._master')

<?php /** @var  Illuminate\Pagination\LengthAwarePaginator  $rows */ ?>

@push('logpage-title')
    @lang('View Logs List')
@endpush

@section('log-content')
   <div class="grid grid-cols-1 py-5 overflow-x-auto">
       <table class="w-full">
           <thead>
           <tr>
               @foreach($headers as $key => $header)
                   <th scope="col" class="{{ $key == 'date' ? 'text-left' : 'text-center' }}">
                       @if ($key == 'date')
                           <span>{{ $header }}</span>
                       @else
                           <span class="level-{{ $key }} px-2 rounded inline-flex items-center">
                            @include('log-viewer::adminator.icon-maker', ['icon' => $key, 'size' => 5]) {{ $header }}
                        </span>
                       @endif
                   </th>
               @endforeach
               <th scope="col" class="text-right">@lang('Actions')</th>
           </tr>
           </thead>
           <tbody>
           @forelse($rows as $date => $row)
               <tr class="{{ $loop->index % 2 === 0 ? 'even' : 'odd' }}">
                   @foreach($row as $key => $value)
                       <td class="{{ $key == 'date' ? 'text-left' : 'text-center' }}">
                           @if ($key == 'date')
                               <span>{{ \Carbon\Carbon::parse($value)->format('d/m/Y') }}</span>
                           @elseif ($value == 0)
                               <span>{{ $value }}</span>
                           @else
                               <a href="{{ route('log-viewer::logs.filter', [$date, $key]) }}">
                                   <span class="{{ $key }}">{{ $value }}</span>
                               </a>
                           @endif
                       </td>
                   @endforeach
                   <td class="text-right flex space-x-5 pr-2 items-center py-1">
                       <a href="{{ route('log-viewer::logs.show', [$date]) }}" class="p-1 bg-blue-500 rounded">
                           <x-icons.trash />
                       </a>
                       <a href="{{ route('log-viewer::logs.download', [$date]) }}" class="p-1 bg-green-500 rounded">
                           <x-icons.trash />
                       </a>
                       <form id="deleteLogForm" method="POST" action="{{ route('log-viewer::logs.delete') }}">
                           @method('DELETE')
                           @csrf
                           <input type="hidden" name="date" value="{{ $date }}">
                           <button type="submit" class="inline flex p-1 bg-red-500 rounded">
                               <x-icons.trash />
                           </button>
                       </form>
                   </td>
               </tr>
           @empty
               <tr>
                   <td colspan="11" class="text-center">
                       <span>@lang('The list of logs is empty!')</span>
                   </td>
               </tr>
           @endforelse
           </tbody>
       </table>
   </div>
    {{ $rows->render() }}
@endsection

@push('before-logscripts')
    <script>
        $(function () {
            var deleteLogForm  = $('#deleteLogForm');
            deleteLogForm.on('submit', function(event) {
                event.preventDefault();
                Swal.fire({
                    title: 'Are you sure you want to permanently delete this item?',
                    showCancelButton: true,
                    confirmButtonText: 'Confirm Permanent Delete',
                    cancelButtonText: 'Cancel',
                    icon: 'warning'
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            url:      $(this).attr('action'),
                            type:     $(this).attr('method'),
                            dataType: 'json',
                            data:     $('#deleteLogForm').serialize(),
                            success: function(data) {
                                if (data.result === 'success') {
                                    location.reload();
                                }
                                else {
                                    alert('AJAX ERROR ! Check the console !');
                                    console.error(data);
                                }
                            },
                            error: function(xhr, textStatus, errorThrown) {
                                alert('AJAX ERROR ! Check the console !');
                                console.error(errorThrown);
                            }
                        });
                        return false;
                    }
                });
            });
        });
    </script>
@endpush
