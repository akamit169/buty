@if(!empty($data))
<?php
    $prevValue = '';
    $i = 0;
?>
@foreach($data as $key=>$value)
    <?php
        $localStartDateTime = \App\Utilities\DateTimeUtility::convertDateTimeToTimezone($value['start_datetime'], $timezone);
        $localEndDateTime = \App\Utilities\DateTimeUtility::convertDateTimeToTimezone($value['end_datetime'], $timezone);
    ?>
    @if($prevValue == '')
    <div class="row">
        <div class="col s10 m9 xl4">
            <div class="today-data">
                @if(date('Y-m-d') == date('Y-m-d', strtotime($localStartDateTime)))
                Today: 
                @endif    
                {{date('l, d F Y', strtotime($localStartDateTime))}}
            </div>
        </div>
        <div class="col s12 m6 xl4 padding-left-none border-right">
            <div class="label-data">
                @if($value['is_available'])
                    @if($i == 0)
                    <span class="slot1FromTime">{{date('h:i A', strtotime($localStartDateTime))}}</span> - <span class="slot1ToTime">{{date('h:i A', strtotime($localEndDateTime))}}</span>
                    @else
                        {{date('h:i A', strtotime($localStartDateTime))}} - {{date('h:i A', strtotime($localEndDateTime))}}
                    @endif
                @else
                    <span class="red-text">Unavailable</span>
                @endif    
                <input type="hidden" class="slot1Date" value="{{date('Y-m-d', strtotime($localStartDateTime))}}"/>
                <input type="hidden" class="id" value="{{$value['id']}}"/>
            </div>
        </div>
        @else
        <div class="col s12 m6 xl3 padding-right-none">
            <div class="label-data">
                @if($value['is_available'])
                    @if($i == 1)
                    <span class="slot2FromTime">{{date('h:i A', strtotime($localStartDateTime))}}</span> - <span class="slot2ToTime">{{date('h:i A', strtotime($localEndDateTime))}}</span>
                    @else
                        {{date('h:i A', strtotime($localStartDateTime))}} - {{date('h:i A', strtotime($localEndDateTime))}}
                    @endif
                    
                @else
                    <span class="red-text">Unavailable</span>
                @endif
                <input type="hidden" class="slot2Date" value="{{date('Y-m-d', strtotime($localStartDateTime))}}"/>
                <input type="hidden" class="id" value="{{$value['id']}}"/>
            </div>
        </div>
        @if($i == 1 && $returnFromSave == 0)
            <a href="#edit-time-modal" class="set-date">Set</a>
        @endif
        </div>
        @endif
        <?php
            $i++;
            if($prevValue == '') {
                $prevValue = $value['start_datetime'];
            } else {
                $prevValue = '';
            }
        ?>
    
@endforeach
@else
<div class="row">
    <div class="col s10 m9 xl4">
        <div class="today-data">
            @if(date('Y-m-d') == $selectedDate)
                Today: 
            @elseif(is_array($selectedDate) && in_array(date('Y-m-d'), $selectedDate))   
                Today:
            @endif    
            @if(is_array($selectedDate))
                {{date('l, d F Y', strtotime($selectedDate[0]))}}
            @else
                {{date('l, d F Y', strtotime($selectedDate))}}
            @endif
        </div>
    </div>
    <div class="col s12 m6 xl4 padding-left-none border-right">
        <div class="label-data">-</div>
    </div>
    <div class="col s12 m6 xl3 padding-right-none">
        <div class="label-data">-</div>
    </div>
    <a href="#edit-time-modal" class="set-date">Set</a>
</div>
@endif