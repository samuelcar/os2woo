@extends('app')

@section('content')
    <div class="row">
        <div class="col-md-3 col-sm-6">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">Products in OSCommerce</h3>
                </div>
                <div class="panel-body">
                    <p>@{{ os_total }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="panel panel-warning">
                <div class="panel-heading">
                    <h3 class="panel-title">Products Imported</h3>
                </div>
                <div class="panel-body">
                    <p>@{{ imported_total }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title">Percentage Imported</h3>
                </div>
                <div class="panel-body">
                    <p>@{{ imported_per }}%</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="panel panel-success">
                <div class="panel-heading">
                    <h3 class="panel-title">Enlapsed Time</h3>
                </div>
                <div class="panel-body">
                    <p>@{{ time }}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3 import-controls" >
            <div class="form-group">

                <label for="" class="control-label">Products to export</label>
                <input type="text" class=" form-control" v-model="to_import" v-attr="disabled: working"
                       placeholder="Amount to export">
            </div>
            <div class="form-group">
                <button class="btn btn-primary" v-show="! working" v-on="click: work">Import</button>
                <button class="btn btn-danger" v-show="working" v-on="click: stop">Stop</button>
            </div>

        </div>
    </div>
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="progress">
                <div class="progress-bar"
                     v-class="progress-bar-striped: working,
                     active: working,
                     progress-bar-success: finished,
                     progress-bar-info: working,
                     progress-bar-warning: !working && !finished"
                     role="progressbar" aria-valuemin="0" aria-valuemax="100"
                     v-style="width: session_per+'%'">
                    <span class="sr-only">@{{ session_per }}% Complete</span>
                </div>
            </div>
        </div>
    </div>
@stop
