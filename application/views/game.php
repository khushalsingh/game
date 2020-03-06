<!doctype html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="//stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">

        <title>Game</title>
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col">
                    <h1>Game</h1>
                    <form action="#" id="game_form" method="post">
                        <div class="form-group">
                            <label for="user_name">Enter Name</label>
                            <input type="text" class="form-control" id="user_name" name="user_name" required="">
                        </div>
                        <div class="form-group">
                            <label for="user_cards">Enter cards</label>
                            <input type="text" class="form-control" id="user_cards" name="user_cards" required="">
                        </div>
                        <button type="submit" class="btn btn-primary">Play Now</button>
                    </form>
                    <br/>
                    Generated Cards<br/>
                    <input type="text" class="form-control" id="generated_cards" name="" readonly="">

                    <hr/>
                    <table class="table table-striped" id="dashboard" style="display: none;">
                        <thead>
                            <tr>
                                <th>USER</th>
                                <th>HANDS WON</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="//code.jquery.com/jquery.min.js"></script>
        <script src="//cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
        <script src="//stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.13.1/jquery.validate.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.13.1/additional-methods.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
        <script>
            $(function () {
                score_card();
                $("#game_form").validate({
                    errorElement: 'span',
                    errorClass: 'help-block text-right',
                    rules: {
                        user_name: {
                            required: true
                        },
                        user_cards: {
                            required: true,
                            remote: {
                                url: '/game/game/validate_cards',
                                type: 'post'
                            }
                        }
                    },
                    messages: {
                        user_name: {
                            required: "Please enter your name."
                        },
                        user_cards: {
                            required: "Please enter cards.",
                            remote: "Valid Cards : 2, 3, 4, 5, 6, 7, 8, 9, 10, J, Q, K, A (separated by space)"
                        }
                    },
                    highlight: function (element) {
                        $(element).closest('.form-group').addClass('has-error');
                    },
                    unhighlight: function (element) {
                        $(element).closest('.form-group').removeClass('has-error');
                    },
                    success: function (element) {
                        $(element).closest('.form-group').removeClass('has-error');
                        $(element).closest('.form-group').children('span.help-block').remove();
                    },
                    errorPlacement: function (error, element) {
                        error.appendTo(element.closest('.form-group'));
                    },
                    submitHandler: function (form) {
                        toastr.remove();
                        $.post('/game/game/play', $("#game_form").serialize(), function (data) {
                            if (data.errors.length === 0) {
                                $("#generated_cards").val(data.generated_cards.join(' '));
                                if (data.user_won == '1') {
                                    toastr.success('Congratulations, You won !!!');
                                } else {
                                    toastr.warning('Oops, You Loose !!!');
                                }
                            } else if (data.errors === '1') {
                                toastr.warning('Invalid Request !!!');
                            } else {
                                toastr.error(data.errors);
                            }
                            score_card();
                        });
                    }
                });
            });

            function score_card() {
                $.get('/game/game/score_card', function (data) {
                    if (data.length > 0) {
                        $("#dashboard").show();
                    }
                    $("#dashboard").children('tbody').html('');
                    $.each(data, function (i, v) {
                        $("#dashboard").children('tbody').append('<tr><td>' + v.user_name + '</td><td>' + v.user_hands_won + '</td></tr>');
                    });
                });
            }
        </script>
    </body>
</html>