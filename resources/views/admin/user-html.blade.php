                           



<a href=" {{url("admin/user/feedback") . '/' . $data['id'] }}" > View Feedback |</a> 
<a href="{{url("admin/user/acr-status").'?id=' . $data['id'] . '&status=' . $status }}" class="status" title='{{$title}}'>{{$title}}</a>