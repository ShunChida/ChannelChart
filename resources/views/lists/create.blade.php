@extends('layouts.app')

@section('content')

    <div class="text-center">
        <h1>リスト作成</h1>
    </div>
    
    <div class="container">
        <div class="row">
            <div class="col-12 col-sm-12 col-md-12">
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        {!! Form::open(['route' => 'lists.store']) !!}
                            <div class="form-group">
                                {!! Form::label('name', 'リスト名:') !!}
                                {!! Form::text('name', null, ['class' => 'form-control']) !!}
                            </div>
                    </div>
                </div>
                
                <div class="container">
                    <div class="row form-group">
                        <div class="container">
                            <div class="row">
                                @for ($i = 0; $i < count($channels); $i++)
                                    <div class="col-12 col-sm-6 col-md-4">
                                        <div class="row">
                                            <div class="container">
                                                <div class="row">
                                                    <div class="custom-control custom-checkbox custom-control-inline" style="margin-bottom: 20px; align-items: center;">
                                                        {{Form::checkbox('selection[]', $channels[$i]['id'], false, ['class'=>'custom-control-input', 'id' => $i])}}
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
                        <div class="col-3">
                            {!! Form::submit('作成', ['class' => 'btn btn-primary btn-block']) !!}
                        </div>
                    </div>
                </div>
                        
                        {!! Form::close() !!}
            </div>
        </div>
    </div>

@endsection