<div id="js">

    <button onclick="sweet_alert_geolocation()" id="alert" style="display: none ; ">new</button>
    <script src="{{ asset('public/plugins/jQuery/jQuery-2.1.4.min.js') }}"></script>
    <!--<script src="https://code.jquery.com/ui/1.10.2/jquery-ui.min.js"></script>-->
    <script src="{{ asset('public/dist/js/jquery-ui.js') }}"></script>
    <script src="{{ asset('public/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('public/plugins/fastclick/fastclick.min.js') }}"></script>
    <!--  // <script src="{{ asset('public/dist/js/app.min.js') }}"></script> -->
    <script src="{{ asset('public/plugins/slimScroll/jquery.slimscroll.min.js') }}"></script>
    <script src="{{ asset('public/plugins/sweetalert/sweetalert.min.js') }}"></script>
    <script src="{{ asset('public/plugins/select2/select2.full.min.js') }}"></script>
    <script src="{{ asset('public/plugins/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ asset('public/plugins/imageloader/jquery.imageloader.js') }}"></script>
    <script src="{{ asset('public/plugins/datepicker/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('public/plugins/timepicker/bootstrap-timepicker.js') }}"></script>
    <script src="{{ asset('public/plugins/iCheck/icheck.min.js') }}"></script>
    <script src="{{ asset('public/dist/js/admin.js') }}"></script>
    <script src="{{ asset('public/dist/js/caret.js') }}"></script>
    <script src="{{ asset('public/dist/js/tag.js') }}"></script>
    @yield('script_upload')
</div>
<script>
    AdminCPLang = {
        @foreach(trans('common') as $key => $value)
        '{{$key}}': '{{ $value }}',
        @endforeach
    }
    function sweet_alert_geolocation() {
        swal('Success', 'Get successfully Location', 'success');
    }
    function formatRepo(repo) {
        if (repo.loading) return repo.text;
        var markup = "<div class='select2-result-repository clearfix'>" + "<div class='select2-result-repository__title'>" + repo.full_name;

        if (repo.district_name) {
            markup += ' , Quận ' + repo.district_name + ' ';
        }
        if (repo.province_name) {
            markup += ' , Thành Phố ' + repo.province_name + ' ';
        }
        return markup + "</div>";
    }
    function formatRepoSelection(repo) {

        var r = repo.full_name;
        if (repo.district_name) {
            r += ' , Quận ' + repo.district_name + ' ';
        }
        if (repo.province_name) {
            r += ' , Thành Phố ' + repo.province_name + ' ';
        }
        return r;
    }
    $(document).ready(function () {
        $('.flag_click').each(function () {
            $(this).hover(function () {
                $('.flag_click').css('color', 'black');
                $(this).css('color', 'red');
            });
            $(this).on('click', function () {
                $('input[name=parent_id]').attr('value', $(this).data('id'));
            })
        });
        $(".js-data-example-ajax").select2({
            ajax: {
                url: "/locations",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.items,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            },
            escapeMarkup: function (markup) {
                return markup;
            },
            minimumInputLength: 3,
            templateResult: formatRepo, // omitted for brevity, see the source of this page
            templateSelection: formatRepoSelection // omitted for brevity, see the source of this page
        });
        $(".ward").select2({
            ajax: {
                url: "/locations?ward=true",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.items,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            },
            escapeMarkup: function (markup) {
                return markup;
            },
            minimumInputLength: 3,
            templateResult: formatRepo, // omitted for brevity, see the source of this page
            templateSelection: formatRepoSelection // omitted for brevity, see the source of this page
        });
        $(".district").select2({
            ajax: {
                url: "/locations?district=true",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.items,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            },
            escapeMarkup: function (markup) {
                return markup;
            },
            minimumInputLength: 1,
            templateResult: formatRepo, // omitted for brevity, see the source of this page
            templateSelection: formatRepoSelection // omitted for brevity, see the source of this page
        });
    });

</script>