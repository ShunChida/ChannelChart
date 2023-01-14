<div class="text-center">
    @if (Request::routeIs('lists.create'))
    <h1>リスト作成</h1>
    @elseif (Request::routeIs('lists.edit'))
    <h1>リスト編集</h1>
    @endif
</div>

<div class="container">
    <div class="row">
        <div class="col-12 col-sm-12 col-md-12">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    @if (Request::routeIs('lists.create'))
                        {!! Form::open(['route' => 'lists.store']) !!}
                    @elseif (Request::routeIs('lists.edit'))
                        {!! Form::open(['route' => ['lists.update', $channel_list->id], 'method' => 'put']) !!}
                    @endif
                    <div class="form-group">
                        {!! Form::label('name', 'リスト名:') !!}
                        {!! Form::text('name', $name, ['class' => 'form-control']) !!}
                    </div>
                </div>
            </div>
            
            <div class="container">
                <div class="row form-group">
                    <div class="container">
                        <div class="row">
                            @for ($i = 0; $i < count($channels); $i++)
                                @php
                                    if (! isset($channel_ids_in_channel_list)) {
                                        $checked_before_edit = false;
                                    } elseif (false !== array_search($channels[$i]['id'], $channel_ids_in_channel_list)) {
                                        $checked_before_edit = true;
                                    } else {
                                        $checked_before_edit = false;
                                    }
                                @endphp
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="row">
                                        <div class="container">
                                            <div class="row">
                                                <div class="custom-control custom-checkbox custom-control-inline" style="margin-bottom: 20px; align-items: center;">
                                                    {{Form::checkbox('selection[]', $channels[$i]['id'], $checked_before_edit, ['class'=>'custom-control-input', 'id' => $i])}}
                                                    <div style="width:60px">
                                                        <img class="rounded-circle img-fluid" style="border: 2px #bbb solid; margin-left: 65px;" src="{{ $channels[$i]['channel']['snippet']['thumbnails']['default']['url'] }}" />
                                                    </div>
                                                    {{Form::label($i, $channels[$i]['channel']['snippet']['title'], ['class'=>'custom-control-label', 'style'=>'cursor: pointer; padding-left: 70px;'])}}
                                               
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
                        
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-4 col-sm-3 col-md-3">
                        @if (Request::routeIs('lists.create'))
                            {!! Form::submit('作成', ['class' => 'btn btn-primary btn-block']) !!}
                        @elseif (Request::routeIs('lists.edit'))
                            {!! Form::submit('保存', ['class' => 'btn btn-success btn-block']) !!}
                        @endif
                        
                    </div>
                </div>
            </div>
                    
                        {!! Form::close() !!}
        </div>
    </div>
</div>