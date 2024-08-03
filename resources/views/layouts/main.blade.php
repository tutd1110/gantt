<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<!-- begin::Head -->
<head>
    <meta charset="utf-8"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    @yield('style')
    <link rel=stylesheet href="{{ asset('assets/platform.css') }}" type="text/css">
    <link rel=stylesheet href="{{ asset('assets/libs/jquery/dateField/jquery.dateField.css') }}" type="text/css">

    <link rel=stylesheet href="{{ asset('assets/gantt.css') }}" type="text/css">
    <link rel=stylesheet href="{{ asset('assets/ganttPrint.css') }}" type="text/css" media="print">
    <link rel=stylesheet href="{{ asset('assets/libs/jquery/valueSlider/mb.slider.css') }}" type="text/css" media="print">

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

    <script src="{{ asset('assets/libs/jquery/jquery.livequery.1.1.1.min.js') }}"></script>
    <script src="{{ asset('assets/libs/jquery/jquery.timers.js') }}"></script>

    <script src="{{ asset('assets/libs/utilities.js') }}"></script>
    <script src="{{ asset('assets/libs/forms.js') }}"></script>
    <script src="{{ asset('assets/libs/date.js') }}"></script>
    <script src="{{ asset('assets/libs/dialogs.js') }}"></script>
    <script src="{{ asset('assets/libs/layout.js') }}"></script>
    <script src="{{ asset('assets/libs/i18nJs.js') }}"></script>
    <script src="{{ asset('assets/libs/jquery/dateField/jquery.dateField.js') }}"></script>
    <script src="{{ asset('assets/libs/jquery/JST/jquery.JST.js') }}"></script>
    <script src="{{ asset('assets/libs/jquery/valueSlider/jquery.mb.slider.js') }}"></script>

    <script type="text/javascript" src="{{ asset('assets/libs/jquery/svg/jquery.svg.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/libs/jquery/svg/jquery.svgdom.1.8.js') }}"></script>


    <script src="{{ asset('assets/ganttUtilities.js') }}"></script>
    <script src="{{ asset('assets/ganttTask.js') }}"></script>
    <script src="{{ asset('assets/ganttDrawerSVG.js') }}"></script>
    <script src="{{ asset('assets/ganttZoom.js') }}"></script>
    <script src="{{ asset('assets/ganttGridEditor.js') }}"></script>
    <script src="{{ asset('assets/ganttMaster.js') }}"></script>
</head>
<!-- end::Head -->

<!-- begin::Body -->
<body>
@yield('content')

@yield('script')


</body>

<!-- end::Body -->
</html>
