<!DOCTYPE html>
<html lang="{{Config::get('app.locale');}}" ng-app="irailapp" ng-controller="PlannerCtrl">
    @include('core.head')
<body>
<div class="wrapper">
    <div id="main">
        @include('core.navigation')
        <div class="container">
            <div class="row routeplanner view1 well" ng-show="planning">
                <div class="col-sm-6">
                    <script type="text/ng-template" id="customTemplate.html">
                        <a>
                            <span bind-html-unsafe="match.label | typeaheadHighlight:query"></span>
                        </a>
                    </script>
                    <div class="form-group">
                        <label for="departure">{{Lang::get('client.fromStation')}}</label>
                        <input type="text" ng-model="departure" placeholder="{{Lang::get('client.typeFromStation')}}" typeahead="station as station.name for station in stations.stations | filter:{name:$viewValue} | limitTo:5" typeahead-template-url="customTemplate.html" class="form-control input-lg">
                    </div>
                    <div class="form-group">
                        <label for="destination">{{Lang::get('client.toStation')}}</label>
                        <input type="text" ng-model="destination" placeholder="{{Lang::get('client.typeToStation')}}" typeahead="station as station.name for station in stations.stations | filter:{name:$viewValue} | limitTo:5" typeahead-template-url="customTemplate.html" class="form-control input-lg">
                    </div>
                    <label for="destination">{{Lang::get('client.chooseDate')}}</label>
                    <div class="datepicker">
                        <datepicker ng-class="time" ng-model="mydate" show-weeks="false"></datepicker>
                    </div>
                    <br/>
                </div>
                <div class="col-sm-6">
                    <label for="destination">{{Lang::get('client.chooseTime')}}</label>
                    <select class="form-control input-lg timepicker" ng-model="timeoption">
                        <option value="depart">{{Lang::get('client.departureAtHour')}}</option>
                        <option value="arrive">{{Lang::get('client.arrivalAtHour')}}</option>
                    </select>
                    <timepicker ng-model="mytime" ng-change="changed()" show-meridian="ismeridian"></timepicker>
                    <br/>
                    <input type="submit" class="btn btn-default btn-lg btn-primary btn-wide" ng-click="save()" value="{{Lang::get('client.confirmSearch')}}">
                    <div class="alert alert-danger" ng-show="data === null">
                        <p ng-show="stationnotfound === true">{{Lang::get('client.errorCheckInput')}}</p>
                        <p ng-show="mytime === undefined">Don't forget to set the time.</p>
                        <p ng-show="mydate === undefined">Don't forget to set the date.</p>
                    </div>
                </div>
            </div>
            <div class="row" ng-show="loading">
                <div class="col-md-12 col-sm-12">
                    <div class="loader">Loading...</div>
                    <h4 class="center lg">{{Lang::get('client.loadingHeader')}}</h4>
                    <p class="small center">{{Lang::get('client.loadingSub')}}</p>
                </div>
            </div>
            <div class="row max-w5" ng-show="error" >
                <div class="col-md-12 col-sm-12">
                    <div class="well">
                        <h1 class="center"><i class="fa fa-support fa-3x center"></i>
                        </h1>
                        <h3>{{Lang::get('client.error')}} <strong>{{Lang::get('client.errorNoRoutes')}}</strong></h3>
                        <p>{{Lang::get('client.errorExplanation')}} <a href="mailto:iRail@list.iRail.be">{{Lang::get('client.errorMail')}}</a>.</p>
                        <br/>
                        <a href="#" ng-click="reset()" class="btn btn-danger btn-lg btn-wide"><i class="fa fa-chevron-left"></i> {{Lang::get('client.errorReturn')}}</a>
                        <br/>
                    </div>
                </div>
            </div>
            <div class="row" ng-show="results">
                <div class="col-md-9 col-sm-8">
                    <h4>
                        {{Lang::get('client.from')}} <strong>@{{departure.name}}</strong> {{Lang::get('client.to')}} <strong>@{{destination.name}}</strong>
                        <br/>
                        {{Lang::get('client.on')}} <strong>@{{mydate | date}}</strong>.
                        <br/>
                        {{Lang::get('client.youWantTo')}}
                        <span ng-show="timeoption=='depart'"><strong>{{Lang::get('client.depart')}} </strong></span>
                        <span ng-show="timeoption=='arrive'"><strong>{{Lang::get('client.arrive')}} </strong></span>
                        {{Lang::get('client.at')}} @{{mytime | date : 'HH:mm' }}.
                    </h4>
                    <hr/>
                    <h5>@{{connections.length}} {{Lang::get('client.routesFoundDescription')}}</h5>
                    <div class="panel-group results" id="accordion">
                        <div class="panel panel-default" ng-repeat="conn in connections">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" ng-href="#result-@{{connections.indexOf(conn)}}">
                                <span class="container33">
                                    <span class="tleft">
                                        @{{ (conn.departure.time)*1000 | date:'HH:mm' }}
                                        &rarr;
                                        @{{ (conn.arrival.time)*1000 | date:'HH:mm' }}
                                    </span>
                                    <span class="tcenter">
                                        <strong>
                                            <i class="fa fa-clock-o"></i> @{{ ((conn.arrival.time-conn.departure.time))/60 }} min
                                        </strong>
                                    </span>
                                    <span class="tright">
                                        @{{ conn.vias.number }}
                                    </span>
                                </span>
                                    </a>
                                </h4>
                            </div>
                            <div id="result-@{{connections.indexOf(conn)}}" class="panel-collapse collapse" ng-class="{in : $first}"  >
                                <ul class="list-group">
                                    <li class="list-group-item">
                                        <span class="badge">@{{ conn.departure.platform }}</span>
                                    <span class="planner-time"><strong>
                                            @{{ (conn.departure.time)*1000 | date:'HH:mm' }}
                                        </strong>
                                    </span>
                                    <span class="planner-station">
                                        @{{ conn.departure.station}}
                                    </span>

                                    </li>
                                    <li class="list-group-item" ng-repeat="stop in conn.vias.via">
                                        &darr; @{{stop.vehicle.replace("BE.NMBS.","")}} <span class="small">(@{{stop.direction.name}})</span>
                                        <br/>
                                        <span class="badge">@{{ stop.departure.platform }}</span>
                                    <span class="planner-time"><strong>
                                            @{{ (stop.departure.time)*1000 | date:'HH:mm' }}
                                        </strong>
                                    </span>
                                    <span class="planner-station">
                                    @{{ stop.station}}
                                    </span>
                                        <br/>
                                    </li>
                                    <li class="list-group-item">
                                        &darr; @{{conn.arrival.vehicle.replace("BE.NMBS.","")}} <span class="small">(@{{conn.arrival.direction.name}})</span>
                                        <br/>
                                        <span class="badge">@{{ conn.arrival.platform }}</span>
                                    <span class="planner-time"><strong>
                                            @{{ (conn.arrival.time)*1000 | date:'HH:mm' }}
                                        </strong></span>
                                    <span class="planner-station">
                                        @{{ conn.arrival.station}}
                                    </span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="visible-print">
                        <br/>
                        <p>
                            This route was planned on iRail.be. Thank you very much for using our webapp.
                        </p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-4 hidden-print">
                    <br/>
                    <div class="btn-group btn-wide btn-botm">
                        <a class="btn btn-default btn-50" ng-click="earlier()">&lt; {{Lang::get('client.rideEarlier')}}</a>
                        <a class="btn btn-default btn-50" ng-click="later()">{{Lang::get('client.rideLater')}} &gt;</a>
                    </div>
                    <div class="btn-group btn-wide btn-botm">
                        <a class="btn btn-default btn-50" ng-click="earliest()">&lt;&lt; {{Lang::get('client.earliestRide')}}</a>
                        <a class="btn btn-default btn-50" ng-click="latest()">{{Lang::get('client.latestRide')}} &gt;&gt;</a>
                    </div>
                    <a class="btn btn-primary btn-wide btn-lg btn-botm" ng-click="reverse()"><i class="fa fa-exchange"></i> {{Lang::get('client.reverse')}}</a>
                    <a class="btn btn-default btn-wide btn-lg btn-botm" ng-click="reset()"><i class="fa fa-undo"></i> {{Lang::get('client.planAnother')}}</a>
                </div>
            </div>
        </div>
    </div>
</div>
    @include('core.footer')
</body>
</html>