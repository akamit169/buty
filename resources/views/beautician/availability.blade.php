@extends('beautician.main-layout.main-layout') @section('title') {{'My Availability'}}@stop @section('content')

<!-- start content area -->
<section class="inner-container top-padding avail-pg">
    <div class="avail-section">
        <div class="calendar-div">
            <div class="cell-header">
                <a href="#" class="move-date">Jump to Today</a>
                <a href="#" class="multi-sel">Select Multiple</a>
            </div>
            <div class="sel-avail-div">
                <div class="dates-div">
                    <div id="datepicker"></div>
                </div>
            </div>
        </div>
        <div class="data-list">
        </div>
    </div>
</section>

<div id="edit-time-modal" class="modal">
    <div class="modal-content">
        <h4 class="popup-date">Multiple dates</h4>
        <div class="row">
            <div class="col s5">
                <input type="text" name="slot1From" class="defaultEntry slot1 slot1From" size="10">
                <div class="drop-down width80">
                    <div class="input-field">
                        <select class="slot1FromTimeZone">
                            <option value="" selected></option>
                            <option value="AM">AM</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col s2 center-lab">
                <label>To</label>
            </div>
            <div class="col s5">
                <input type="text" name="slot1To" class="defaultEntry slot1 slot1To" size="10">
                <div class="drop-down width80">
                    <div class="input-field">
                        <select class="slot1ToTimeZone">
                            <option value="" selected></option>
                            <option value="PM">PM</option>
                            <option value="AM">AM</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col s7 disb-time">
                <div class="check-label">Unavailable</div>
                <div class="checkboxGroup">
                    <input type="checkbox" id="u1" class="checks slot1-checkbox avail-check1" />
                    <label for="u1" class=""></label>
                </div>
            </div>
        </div>
        <div class="row margin-bottom-none">
            <div class="col s5">
                <input type="text" name="slot2From" class="defaultEntry slot2 slot2From" size="10">
                <div class="drop-down width80">
                    <div class="input-field">
                        <select class="slot2FromTimeZone">
                            <option value="" selected></option>
                            <option value="PM">PM</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col s2 center-lab">
                <label>To</label>
            </div>
            <div class="col s5">
                <input type="text" name="slot2To" class="defaultEntry slot2 slot2To" size="10">
                <div class="drop-down width80">
                    <div class="input-field">
                        <select class="slot2ToTimeZone">
                            <option value="" selected></option>
                            <option value="AM">AM</option>
                            <option value="PM">PM</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col s7 disb-time">
                <div class="check-label">Unavailable</div>
                <div class="checkboxGroup">
                    <input type="checkbox" id="u2" class="checks slot2-checkbox avail-check2" />
                    <label for="u2" class=""></label>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <a href="#!" class="modal-action modal-close waves-effect border-btn">Cancel</a>
        <a href="#!" class="modal-action set-availability-dt bg-btn waves-effect">Set</a>
    </div>
</div>
<script type="text/javascript">
    var arePaymentDetailsSet = <?php echo $arePaymentDetailsSet; ?>
</script>
@stop @section('scriptjs')
<script type="text/javascript" src="{{URL::asset('assets/beautician/js/jquery-ui.min.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('assets/beautician/js/jquery-ui.min.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('assets/beautician/js/calendar.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('assets/beautician/js/availability.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('assets/beautician/js/jquery.plugin.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('assets/beautician/js/jquery.timeentry.js')}}"></script>
@stop