<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Ticket Timer</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
<!--JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<!-- vue,.js -->
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.10/dist/vue.js"></script>
<!-- css -->
    <!-- Styles -->
    <style>
        html, body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Nunito', sans-serif;
            font-weight: 200;
            height: 100vh;
            margin: 0;
        }
        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }
        .ticket-summary {
          display: flex;
        }
        .ticket-open-icon {
          margin-left: auto;
        }

        /*--ハンバーガーメニュー--*/
        header {
            padding:10px;
            background: skyblue;
        }
        #nav-drawer  {
            position: relative;
        }
        .nav-unshown {
            display: none;
        }
        #nav-open {
            display: inline-block;
            width: 30px;
            height: 22px;
            vertical-align: middle;
        }
        #nav-open span, #nav-open span:before, #nav-open span:after {
            position: absolute;
            height: 3px;
            width: 25px;
            border-radius: 3px;
            background: #555;
            display: block;
            content: '';
            cursor: pointer;
        }
        #nav-open span:before {
            bottom: -8px;
        }
        #nav-open span:after {
            bottom: -16px;
        }
        #nav-close {
            position: fixed;
            z-index: 99;
            top:  0;
            width: 30%;
            right: 0%;
            height: 100%;
            background: black;
            opacity: 0;
            transition: .3s ease-in-out;
        }
        #nav-content {
            overflow: auto;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 9999;/*最前面に*/
            width: 70%;/*右側に隙間を作る（閉じるカバーを表示）*/
            max-width: 330px;/*最大幅（調整してください）*/
            height: 100%;
            background: #fff;/*背景色*/
            transition: .3s ease-in-out;/*滑らかに表示*/
            -webkit-transform: translateX(-105%);
            transform: translateX(-105%);/*左に隠しておく*/
        }
        /*チェックが入ったらもろもろ表示*/
        #nav-input:checked ~ #nav-close {
            display: block;/*カバーを表示*/
            opacity: .5;
        } 
        #nav-input:checked ~ #nav-content {
            -webkit-transform: translateX(0%);
            transform: translateX(0%);/*中身を表示（右へスライド）*/
            box-shadow: 6px 0 25px rgba(0,0,0,.15);
        }
    </style>

<!-- icon -->
    <script src="https://unpkg.com/ionicons@5.0.0/dist/ionicons.js"></script>

</head>
<body>
<div id="app">
    <div class="flex-center position-ref full-height">
        @if (Route::has('login'))
            <div class="top-right links">
                @auth
                    <a href="{{ url('/home') }}">Home</a>
                @else
                    <a href="{{ route('login') }}">Login</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}">Register</a>
                    @endif
                @endauth
            </div>
        @endif
        <header>
    <!-- nav -->
            <nav id="nav-drawer">
                <input id="nav-input" type="checkbox" class="nav-unshown">
                <label id="nav-open" for="nav-input"><span></span></label>
                <label id="nav-close" for="nav-input">close</label>
                <div id="nav-content">
                    <ul class="main-nav">
                        <li><a href="#">home</a></li>
                        <li><a href="#">setting</a></li>
                        <li><a href="#">logout</a></li>
                    </ul>
                </div>
            </nav>
<!-- nav -->
        </header>
<!-- ticket open-ticket-bar -->
        <div class="wrapper">
            <ul class="list-group">
                <li class="list-group-item" v-for="ticket in tickets">@{{ ticket.text }}</li>
            </ul>
        </div>
<!-- ticket open-ticket-bar -->

<!-- ticket contents  -->
<!--            <ul>
                <li v-for="ticket in tickets">
                    <ul>
                        <li class="list-group-item">
                            <div class="ticket-summary">
                                <div v-text="ticket.text"></div>
                                <div class="ticket-open-icon" v-on:click="ticket.openFlag = !ticket.openFlag">
                                    <ion-icon v-if="!ticket.openFlag" name="caret-forward-outline"></ion-icon>
                                    <ion-icon v-if="ticket.openFlag" name="caret-down-outline"></ion-icon>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item" v-if="ticket.openFlag">
                            <div class="container">
                                <div class="row">
                                    <div class="col-xs-10">
                                        <div class="">
                                            <span>@{{ ticket.id }}</span><span>@{{ ticket.parentId }}</span>
                                        </div>
                                        <span class="text" v-if="!edit" v-text="ticket.text" v-on:click="edit = true"></span>
                                        <span class="edit-icon" v-on:click="edit = true">edit</span>
                                        <input v-if="edit" type="text" v-model="ticket.text" v-on:blur="edit = false"  v-auto-focus>
                                    </div>
                                    <div class="col-xs-2">
                                        <button class="btn btn-primary" v-on:click="createChild(ticket.id)">+</button>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul> -->
<!-- ticket contents -->
        <div align="center">
            <button class="btn btn-primary" v-on:click="createBros(parentId)">+</button>
        </div>
    </div>
</div>
    <script>
      Vue.directive('auto-focus', {
        bind: function () {
          var el = this.el;
          Vue.nextTick(function(){
              el.focus();
          });
        }
      })
      const openTicket = new Vue({
        el: "#app",
        data: {
          ticketNum: 1,
          tickets: [
            { id: 1,
              parentId: -1,
              text: 'test ticket1',
              openFlag: false,
            }
          ],
          edit: false
       },
        methods: {
          createBros: function(parentId) {
            let newTicket = {
              id: this.ticketNum,
              parentId: parentId,
              text: "new ticket",
              openFlag: true,
            }
            this.tickets.push(newTicket)
          }
        }
      })
    </script>
  </body>
</html>


