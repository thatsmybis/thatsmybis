@extends('layouts.app')
@section('title', __("Donate") . " - " . config('app.name'))

@section('content')
<div class="container-fluid container-width-capped">
    <div class="row bg-light mb-3 pt-5 pb-5 rounded">
        <div class="col-xl-8 offset-xl-2 col-md-10 offset-md-1 col-12">
            <h1>
                <span class="fa fa-fw fas fa-heart text-danger"></span>
                {{ __("Support the developer!") }}
            </h1>

            <p class="mt-3">
                {!! __("Hello! My name is Lemmings19 and I am the author of this That's My BIS. I've spent <a href=':link1' target='_blank'>hundreds upon hundreds</a> of hours building, maintaining, and supporting TMB and its community.", ['link1' => 'https://github.com/thatsmybis/thatsmybis/graphs/contributors']) !!}
            </p>
            <p class="mt-3">
                {{ __("Everything you donate will go back into development, and not to some CEO's yacht...") }}
            </p>
            <p class="mt-3">
                {!! __("If you want to reach out to me directly, email is best (<a href=':email1' target='_blank'>:email1</a>). Sometimes I am on the <a href=':link1' target='_blank'>That's My BIS Discord</a>. My username is Lemmings19#1149.", ['link1' => env('APP_DISCORD'), 'email1' => 'lemmings19@gmail.com']) !!}
            </p>
            <p class="mt-3">
                {{ __("None of this would be here without the generous donors listed below, and the kind folks who have helped contribute their time.") }} <span class="text-danger">&lt;3 &lt;3 &lt;3</span>
            </p>
            <p class="mt-3">
                {{ __("May you get all of your BIS!") }}
                <br>
                - Lemmings19
            </p>
            <ul class="list-inline mt-5">
                <li class="list-inline-item mb-3">
                    <a class="text-4 text-patreon patreon-button p-3" href="https://www.patreon.com/lemmings19" target="_blank" title="Patreon donations">
                        <span class="fab fa-fw fa-patreon text-white"></span>
                        Patreon
                    </a>
                </li>
                <li class="list-inline-item">
                    <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
                        <input type="hidden" name="cmd" value="_s-xclick" />
                        <input type="hidden" name="hosted_button_id" value="DFED8CSU2JDS2" />
                        <!-- <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" title="PayPal - The safer, easier way to pay online!" alt="Donate with PayPal button" /> -->
                        <button class="btn btn-lg btn-paypal strong" name="submit" title="PayPal - The safer, easier way to donate online!">
                            <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjQiIGhlaWdodD0iMzIiIHZpZXdCb3g9IjAgMCAyNCAzMiIgcHJlc2VydmVBc3BlY3RSYXRpbz0ieE1pbllNaW4gbWVldCIgeG1sbnM9Imh0dHA6JiN4MkY7JiN4MkY7d3d3LnczLm9yZyYjeDJGOzIwMDAmI3gyRjtzdmciPjxwYXRoIGZpbGw9IiMwMDljZGUiIG9wYWNpdHk9IjEiIGQ9Ik0gMjAuOTI0IDcuMTU3IEMgMjEuMjA0IDUuMDU3IDIwLjkyNCAzLjY1NyAxOS44MDEgMi4zNTcgQyAxOC41ODMgMC45NTcgMTYuNDMgMC4yNTcgMTMuNzE2IDAuMjU3IEwgNS43NTggMC4yNTcgQyA1LjI5IDAuMjU3IDQuNzI5IDAuNzU3IDQuNjM0IDEuMjU3IEwgMS4zNTggMjMuNDU3IEMgMS4zNTggMjMuODU3IDEuNjM5IDI0LjM1NyAyLjEwNyAyNC4zNTcgTCA2Ljk3NSAyNC4zNTcgTCA2LjY5NCAyNi41NTcgQyA2LjYgMjYuOTU3IDYuODgxIDI3LjI1NyA3LjI1NSAyNy4yNTcgTCAxMS4zNzUgMjcuMjU3IEMgMTEuODQ0IDI3LjI1NyAxMi4zMTEgMjYuOTU3IDEyLjQwNSAyNi40NTcgTCAxMi40MDUgMjYuMTU3IEwgMTMuMjQ3IDIwLjk1NyBMIDEzLjI0NyAyMC43NTcgQyAxMy4zNDEgMjAuMjU3IDEzLjgwOSAxOS44NTcgMTQuMjc3IDE5Ljg1NyBMIDE0Ljg0IDE5Ljg1NyBDIDE4Ljg2NCAxOS44NTcgMjEuOTU0IDE4LjE1NyAyMi44OSAxMy4xNTcgQyAyMy4zNTggMTEuMDU3IDIzLjE3MiA5LjM1NyAyMi4wNDggOC4xNTcgQyAyMS43NjcgNy43NTcgMjEuMjk4IDcuNDU3IDIwLjkyNCA3LjE1NyBMIDIwLjkyNCA3LjE1NyI+PC9wYXRoPjxwYXRoIGZpbGw9IiMwMTIxNjkiIG9wYWNpdHk9IjEiIGQ9Ik0gMjAuOTI0IDcuMTU3IEMgMjEuMjA0IDUuMDU3IDIwLjkyNCAzLjY1NyAxOS44MDEgMi4zNTcgQyAxOC41ODMgMC45NTcgMTYuNDMgMC4yNTcgMTMuNzE2IDAuMjU3IEwgNS43NTggMC4yNTcgQyA1LjI5IDAuMjU3IDQuNzI5IDAuNzU3IDQuNjM0IDEuMjU3IEwgMS4zNTggMjMuNDU3IEMgMS4zNTggMjMuODU3IDEuNjM5IDI0LjM1NyAyLjEwNyAyNC4zNTcgTCA2Ljk3NSAyNC4zNTcgTCA4LjI4NiAxNi4wNTcgTCA4LjE5MiAxNi4zNTcgQyA4LjI4NiAxNS43NTcgOC43NTQgMTUuMzU3IDkuMzE1IDE1LjM1NyBMIDExLjY1NSAxNS4zNTcgQyAxNi4yNDMgMTUuMzU3IDE5LjgwMSAxMy4zNTcgMjAuOTI0IDcuNzU3IEMgMjAuODMxIDcuNDU3IDIwLjkyNCA3LjM1NyAyMC45MjQgNy4xNTciPjwvcGF0aD48cGF0aCBmaWxsPSIjMDAzMDg3IiBvcGFjaXR5PSIxIiBkPSJNIDkuNTA0IDcuMTU3IEMgOS41OTYgNi44NTcgOS43ODQgNi41NTcgMTAuMDY1IDYuMzU3IEMgMTAuMjUxIDYuMzU3IDEwLjM0NSA2LjI1NyAxMC41MzIgNi4yNTcgTCAxNi43MTEgNi4yNTcgQyAxNy40NjEgNi4yNTcgMTguMjA4IDYuMzU3IDE4Ljc3MiA2LjQ1NyBDIDE4Ljk1OCA2LjQ1NyAxOS4xNDYgNi40NTcgMTkuMzMzIDYuNTU3IEMgMTkuNTIgNi42NTcgMTkuNzA3IDYuNjU3IDE5LjgwMSA2Ljc1NyBDIDE5Ljg5NCA2Ljc1NyAxOS45ODcgNi43NTcgMjAuMDgyIDYuNzU3IEMgMjAuMzYyIDYuODU3IDIwLjY0MyA3LjA1NyAyMC45MjQgNy4xNTcgQyAyMS4yMDQgNS4wNTcgMjAuOTI0IDMuNjU3IDE5LjgwMSAyLjI1NyBDIDE4LjY3NyAwLjg1NyAxNi41MjUgMC4yNTcgMTMuODA5IDAuMjU3IEwgNS43NTggMC4yNTcgQyA1LjI5IDAuMjU3IDQuNzI5IDAuNjU3IDQuNjM0IDEuMjU3IEwgMS4zNTggMjMuNDU3IEMgMS4zNTggMjMuODU3IDEuNjM5IDI0LjM1NyAyLjEwNyAyNC4zNTcgTCA2Ljk3NSAyNC4zNTcgTCA4LjI4NiAxNi4wNTcgTCA5LjUwNCA3LjE1NyBaIj48L3BhdGg+PC9zdmc+"
                                alt=""
                                class="">
                            <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjMyIiB2aWV3Qm94PSIwIDAgMTAwIDMyIiBwcmVzZXJ2ZUFzcGVjdFJhdGlvPSJ4TWluWU1pbiBtZWV0IiB4bWxucz0iaHR0cDomI3gyRjsmI3gyRjt3d3cudzMub3JnJiN4MkY7MjAwMCYjeDJGO3N2ZyI+PHBhdGggZmlsbD0iIzAwMzA4NyIgZD0iTSAxMi4yMzcgMi40NDQgTCA0LjQzNyAyLjQ0NCBDIDMuOTM3IDIuNDQ0IDMuNDM3IDIuODQ0IDMuMzM3IDMuMzQ0IEwgMC4yMzcgMjMuMzQ0IEMgMC4xMzcgMjMuNzQ0IDAuNDM3IDI0LjA0NCAwLjgzNyAyNC4wNDQgTCA0LjUzNyAyNC4wNDQgQyA1LjAzNyAyNC4wNDQgNS41MzcgMjMuNjQ0IDUuNjM3IDIzLjE0NCBMIDYuNDM3IDE3Ljc0NCBDIDYuNTM3IDE3LjI0NCA2LjkzNyAxNi44NDQgNy41MzcgMTYuODQ0IEwgMTAuMDM3IDE2Ljg0NCBDIDE1LjEzNyAxNi44NDQgMTguMTM3IDE0LjM0NCAxOC45MzcgOS40NDQgQyAxOS4yMzcgNy4zNDQgMTguOTM3IDUuNjQ0IDE3LjkzNyA0LjQ0NCBDIDE2LjgzNyAzLjE0NCAxNC44MzcgMi40NDQgMTIuMjM3IDIuNDQ0IFogTSAxMy4xMzcgOS43NDQgQyAxMi43MzcgMTIuNTQ0IDEwLjUzNyAxMi41NDQgOC41MzcgMTIuNTQ0IEwgNy4zMzcgMTIuNTQ0IEwgOC4xMzcgNy4zNDQgQyA4LjEzNyA3LjA0NCA4LjQzNyA2Ljg0NCA4LjczNyA2Ljg0NCBMIDkuMjM3IDYuODQ0IEMgMTAuNjM3IDYuODQ0IDExLjkzNyA2Ljg0NCAxMi42MzcgNy42NDQgQyAxMy4xMzcgOC4wNDQgMTMuMzM3IDguNzQ0IDEzLjEzNyA5Ljc0NCBaIj48L3BhdGg+PHBhdGggZmlsbD0iIzAwMzA4NyIgZD0iTSAzNS40MzcgOS42NDQgTCAzMS43MzcgOS42NDQgQyAzMS40MzcgOS42NDQgMzEuMTM3IDkuODQ0IDMxLjEzNyAxMC4xNDQgTCAzMC45MzcgMTEuMTQ0IEwgMzAuNjM3IDEwLjc0NCBDIDI5LjgzNyA5LjU0NCAyOC4wMzcgOS4xNDQgMjYuMjM3IDkuMTQ0IEMgMjIuMTM3IDkuMTQ0IDE4LjYzNyAxMi4yNDQgMTcuOTM3IDE2LjY0NCBDIDE3LjUzNyAxOC44NDQgMTguMDM3IDIwLjk0NCAxOS4zMzcgMjIuMzQ0IEMgMjAuNDM3IDIzLjY0NCAyMi4xMzcgMjQuMjQ0IDI0LjAzNyAyNC4yNDQgQyAyNy4zMzcgMjQuMjQ0IDI5LjIzNyAyMi4xNDQgMjkuMjM3IDIyLjE0NCBMIDI5LjAzNyAyMy4xNDQgQyAyOC45MzcgMjMuNTQ0IDI5LjIzNyAyMy45NDQgMjkuNjM3IDIzLjk0NCBMIDMzLjAzNyAyMy45NDQgQyAzMy41MzcgMjMuOTQ0IDM0LjAzNyAyMy41NDQgMzQuMTM3IDIzLjA0NCBMIDM2LjEzNyAxMC4yNDQgQyAzNi4yMzcgMTAuMDQ0IDM1LjgzNyA5LjY0NCAzNS40MzcgOS42NDQgWiBNIDMwLjMzNyAxNi44NDQgQyAyOS45MzcgMTguOTQ0IDI4LjMzNyAyMC40NDQgMjYuMTM3IDIwLjQ0NCBDIDI1LjAzNyAyMC40NDQgMjQuMjM3IDIwLjE0NCAyMy42MzcgMTkuNDQ0IEMgMjMuMDM3IDE4Ljc0NCAyMi44MzcgMTcuODQ0IDIzLjAzNyAxNi44NDQgQyAyMy4zMzcgMTQuNzQ0IDI1LjEzNyAxMy4yNDQgMjcuMjM3IDEzLjI0NCBDIDI4LjMzNyAxMy4yNDQgMjkuMTM3IDEzLjY0NCAyOS43MzcgMTQuMjQ0IEMgMzAuMjM3IDE0Ljk0NCAzMC40MzcgMTUuODQ0IDMwLjMzNyAxNi44NDQgWiI+PC9wYXRoPjxwYXRoIGZpbGw9IiMwMDMwODciIGQ9Ik0gNTUuMzM3IDkuNjQ0IEwgNTEuNjM3IDkuNjQ0IEMgNTEuMjM3IDkuNjQ0IDUwLjkzNyA5Ljg0NCA1MC43MzcgMTAuMTQ0IEwgNDUuNTM3IDE3Ljc0NCBMIDQzLjMzNyAxMC40NDQgQyA0My4yMzcgOS45NDQgNDIuNzM3IDkuNjQ0IDQyLjMzNyA5LjY0NCBMIDM4LjYzNyA5LjY0NCBDIDM4LjIzNyA5LjY0NCAzNy44MzcgMTAuMDQ0IDM4LjAzNyAxMC41NDQgTCA0Mi4xMzcgMjIuNjQ0IEwgMzguMjM3IDI4LjA0NCBDIDM3LjkzNyAyOC40NDQgMzguMjM3IDI5LjA0NCAzOC43MzcgMjkuMDQ0IEwgNDIuNDM3IDI5LjA0NCBDIDQyLjgzNyAyOS4wNDQgNDMuMTM3IDI4Ljg0NCA0My4zMzcgMjguNTQ0IEwgNTUuODM3IDEwLjU0NCBDIDU2LjEzNyAxMC4yNDQgNTUuODM3IDkuNjQ0IDU1LjMzNyA5LjY0NCBaIj48L3BhdGg+PHBhdGggZmlsbD0iIzAwOWNkZSIgZD0iTSA2Ny43MzcgMi40NDQgTCA1OS45MzcgMi40NDQgQyA1OS40MzcgMi40NDQgNTguOTM3IDIuODQ0IDU4LjgzNyAzLjM0NCBMIDU1LjczNyAyMy4yNDQgQyA1NS42MzcgMjMuNjQ0IDU1LjkzNyAyMy45NDQgNTYuMzM3IDIzLjk0NCBMIDYwLjMzNyAyMy45NDQgQyA2MC43MzcgMjMuOTQ0IDYxLjAzNyAyMy42NDQgNjEuMDM3IDIzLjM0NCBMIDYxLjkzNyAxNy42NDQgQyA2Mi4wMzcgMTcuMTQ0IDYyLjQzNyAxNi43NDQgNjMuMDM3IDE2Ljc0NCBMIDY1LjUzNyAxNi43NDQgQyA3MC42MzcgMTYuNzQ0IDczLjYzNyAxNC4yNDQgNzQuNDM3IDkuMzQ0IEMgNzQuNzM3IDcuMjQ0IDc0LjQzNyA1LjU0NCA3My40MzcgNC4zNDQgQyA3Mi4yMzcgMy4xNDQgNzAuMzM3IDIuNDQ0IDY3LjczNyAyLjQ0NCBaIE0gNjguNjM3IDkuNzQ0IEMgNjguMjM3IDEyLjU0NCA2Ni4wMzcgMTIuNTQ0IDY0LjAzNyAxMi41NDQgTCA2Mi44MzcgMTIuNTQ0IEwgNjMuNjM3IDcuMzQ0IEMgNjMuNjM3IDcuMDQ0IDYzLjkzNyA2Ljg0NCA2NC4yMzcgNi44NDQgTCA2NC43MzcgNi44NDQgQyA2Ni4xMzcgNi44NDQgNjcuNDM3IDYuODQ0IDY4LjEzNyA3LjY0NCBDIDY4LjYzNyA4LjA0NCA2OC43MzcgOC43NDQgNjguNjM3IDkuNzQ0IFoiPjwvcGF0aD48cGF0aCBmaWxsPSIjMDA5Y2RlIiBkPSJNIDkwLjkzNyA5LjY0NCBMIDg3LjIzNyA5LjY0NCBDIDg2LjkzNyA5LjY0NCA4Ni42MzcgOS44NDQgODYuNjM3IDEwLjE0NCBMIDg2LjQzNyAxMS4xNDQgTCA4Ni4xMzcgMTAuNzQ0IEMgODUuMzM3IDkuNTQ0IDgzLjUzNyA5LjE0NCA4MS43MzcgOS4xNDQgQyA3Ny42MzcgOS4xNDQgNzQuMTM3IDEyLjI0NCA3My40MzcgMTYuNjQ0IEMgNzMuMDM3IDE4Ljg0NCA3My41MzcgMjAuOTQ0IDc0LjgzNyAyMi4zNDQgQyA3NS45MzcgMjMuNjQ0IDc3LjYzNyAyNC4yNDQgNzkuNTM3IDI0LjI0NCBDIDgyLjgzNyAyNC4yNDQgODQuNzM3IDIyLjE0NCA4NC43MzcgMjIuMTQ0IEwgODQuNTM3IDIzLjE0NCBDIDg0LjQzNyAyMy41NDQgODQuNzM3IDIzLjk0NCA4NS4xMzcgMjMuOTQ0IEwgODguNTM3IDIzLjk0NCBDIDg5LjAzNyAyMy45NDQgODkuNTM3IDIzLjU0NCA4OS42MzcgMjMuMDQ0IEwgOTEuNjM3IDEwLjI0NCBDIDkxLjYzNyAxMC4wNDQgOTEuMzM3IDkuNjQ0IDkwLjkzNyA5LjY0NCBaIE0gODUuNzM3IDE2Ljg0NCBDIDg1LjMzNyAxOC45NDQgODMuNzM3IDIwLjQ0NCA4MS41MzcgMjAuNDQ0IEMgODAuNDM3IDIwLjQ0NCA3OS42MzcgMjAuMTQ0IDc5LjAzNyAxOS40NDQgQyA3OC40MzcgMTguNzQ0IDc4LjIzNyAxNy44NDQgNzguNDM3IDE2Ljg0NCBDIDc4LjczNyAxNC43NDQgODAuNTM3IDEzLjI0NCA4Mi42MzcgMTMuMjQ0IEMgODMuNzM3IDEzLjI0NCA4NC41MzcgMTMuNjQ0IDg1LjEzNyAxNC4yNDQgQyA4NS43MzcgMTQuOTQ0IDg1LjkzNyAxNS44NDQgODUuNzM3IDE2Ljg0NCBaIj48L3BhdGg+PHBhdGggZmlsbD0iIzAwOWNkZSIgZD0iTSA5NS4zMzcgMi45NDQgTCA5Mi4xMzcgMjMuMjQ0IEMgOTIuMDM3IDIzLjY0NCA5Mi4zMzcgMjMuOTQ0IDkyLjczNyAyMy45NDQgTCA5NS45MzcgMjMuOTQ0IEMgOTYuNDM3IDIzLjk0NCA5Ni45MzcgMjMuNTQ0IDk3LjAzNyAyMy4wNDQgTCAxMDAuMjM3IDMuMTQ0IEMgMTAwLjMzNyAyLjc0NCAxMDAuMDM3IDIuNDQ0IDk5LjYzNyAyLjQ0NCBMIDk2LjAzNyAyLjQ0NCBDIDk1LjYzNyAyLjQ0NCA5NS40MzcgMi42NDQgOTUuMzM3IDIuOTQ0IFoiPjwvcGF0aD48L3N2Zz4="
                                alt="PayPal"
                                class="">
                        </button>
                        <img alt="" border="0" src="https://www.paypal.com/en_CA/i/scr/pixel.gif" width="1" height="1" />
                    </form>
                </li>
            </ul>
        </div>
        <div class="col-xl-8 offset-xl-2 col-md-10 offset-md-1 col-12 mb-4">
            <h1 class="text-warning">
                <span class="fa fa-fw fas fa-exclamation-triangle"></span>
                {{ __("To disable ads, support on Patreon!") }}
            </h1>
            <p class="mt-3">
                {!! __("Subscribe on <a href=':link1' target='_blank'>Patreon</a> and I will disable ads for your account for the duration of your subscription. You will also be supporting the site.", ['link1' => 'https://www.patreon.com/lemmings19']) !!}
            </p>
            <p class="mt-3">
                {!! __("<strong>It may take up to 48 hours before ads are turned off.</strong> I currently need to manually check subscriptions to update this.") !!}
            </p>
            <p class="mt-3">
                {!! __("Donate on PayPal (and include your Discord username) and I will disable ads on your account based on the donation. You <strong>must</strong> include your Discord username in the donation message for ads to be turned off.") !!}
            </p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 col-12 pl-2 pr-2">
            <div class="bg-light rounded mb-3 pt-5 pb-5 col-12">
                <h2 class="text-epic">
                    <span class="fas fa-fw text-legendary"><img class="inline-image-icon" src="{{ asset('images/gargul.png') }}"></img></span>
                    <a href="https://www.curseforge.com/wow/addons/gargul" target="_blank" class="text-epic">
                        {{ __("Gargul Addon") }}
                    </a>
                </h2>
                <p class="mt-3">
                    {!! __("<a href=':link1' target='_blank'>Gargul</a> is an addon developed with love, and independently of TMB. Its author is Zhorax#1454.", ['link1' => 'https://www.curseforge.com/wow/addons/gargul']) !!}
                </p>
            </div>
        </div>
        <div class="col-md-6 col-12 pl-2 pr-2">
            <div class="bg-light rounded mb-3 pt-5 pb-5 col-12">
                <h2>
                    <span class="fab fa-fw fa-battle-net text-mage"></span>
                    <a href="https://www.curseforge.com/wow/addons/tmb-helper" target="_blank" class="text-uncommon">
                        {{ __("TMB Tooltips Addon") }}
                    </a>
                </h2>
                <p class="mt-3">
                    {!! __("<a href=':link1' target='_blank'>TMB Tooltips</a> is an addon developed with love, and independently of TMB. Its author is Strix#1000.", ['link1' => 'https://www.curseforge.com/wow/addons/tmb-helper']) !!}
                </p>
            </div>
        </div>
    </div>

    @php
        $donors = [
            ['name' => 'A4uronn',             'icon' => 'crown', 'title' => 'Raider'],
            ['name' => 'Adilar/Myras',        'icon' => 'crown', 'title' => 'Parser'],
            ['name' => 'Aethil',              'icon' => 'crown', 'title' => 'Parser'],
            ['name' => 'Amatyr',              'icon' => 'crown', 'title' => 'Raider'],
            ['name' => 'andrew_g',            'icon' => 'crown', 'title' => 'Raider'],
            ['name' => 'Anonymous',           'icon' => 'crown', 'title' => 'Patreon'],
            ['name' => 'Anthony',             'icon' => 'crown', 'title' => 'Parser'],
            ['name' => 'Arcadia',             'icon' => 'crown', 'title' => 'Raider'],
            ['name' => 'Arma',                'icon' => 'crown', 'title' => 'Raider'],
            ['name' => 'bakedbread',          'icon' => 'crown', 'title' => 'Raider'],
            ['name' => 'Bianca',              'icon' => 'crown', 'title' => 'Parser'],
            ['name' => 'Blezner',             'icon' => 'crown', 'title' => 'Parser'],
            ['name' => 'botnet',              'icon' => 'crown', 'title' => 'Parser'],
            ['name' => 'Braeo',               'icon' => 'crown', 'title' => 'Parser'],
            ['name' => 'BurnHavoc',           'icon' => 'crown', 'title' => 'Raider'],
            ['name' => 'Busmonstret',         'icon' => 'crown', 'title' => 'Raider'],
            ['name' => 'Calaris',             'icon' => 'crown', 'title' => 'Main Tank'],
            ['name' => 'Craig',               'icon' => 'crown', 'title' => 'Parser'],
            ['name' => 'diadia',              'icon' => 'crown', 'title' => 'Main Tank'],
            ['name' => 'Drezdan',             'icon' => 'crown', 'title' => 'Raider'],
            ['name' => 'EmpKain',             'icon' => 'crown', 'title' => 'Parser'],
            ['name' => 'Euredraith',          'icon' => 'crown', 'title' => 'Parser'],
            ['name' => 'Faelor',              'icon' => 'crown', 'title' => 'Raider'],
            ['name' => 'Feora',               'icon' => 'crown', 'title' => 'Main Tank'],
            ['name' => 'Finvy',               'icon' => 'crown', 'title' => 'Raider'],
            ['name' => 'Fragtoaster1',        'icon' => 'crown', 'title' => 'Raider'],
            ['name' => 'Freddykr',            'icon' => 'crown', 'title' => 'Raider'],
            ['name' => 'Garsidian Games',     'icon' => 'crown', 'title' => 'Main Tank'],
            ['name' => 'GrumpyOwl',           'icon' => 'crown', 'title' => 'Raider'],
            ['name' => 'Hadiya',              'icon' => 'crown', 'title' => 'Main Tank'],
            ['name' => 'Hello',               'icon' => 'crown', 'title' => 'Raider'],
            ['name' => 'InY0f4c3#3214',       'icon' => 'crown', 'title' => 'Main Tank'],
            ['name' => 'ITank',               'icon' => 'crown', 'title' => 'Raider'],
            ['name' => 'Jahare#3676',         'icon' => 'crown', 'title' => 'Parser'],
            ['name' => 'Jensok',              'icon' => 'crown', 'title' => 'Raider'],
            ['name' => 'Jetor',               'icon' => 'crown', 'title' => 'Parser'],
            ['name' => 'Junior',              'icon' => 'crown', 'title' => 'Main Tank'],
            ['name' => 'Kazczyk',             'icon' => 'crown', 'title' => 'Parser'],
            ['name' => 'Keckterz',            'icon' => 'crown', 'title' => 'Raider'],
            ['name' => 'Khashte',             'icon' => 'crown', 'title' => 'Parser'],
            ['name' => 'Kirk',                'icon' => 'crown', 'title' => 'Parser'],
            ['name' => 'Kral',                'icon' => 'crown', 'title' => 'Parser'],
            ['name' => 'Kroxz',               'icon' => 'crown', 'title' => 'Raider'],
            ['name' => 'kzEr',                'icon' => 'crown', 'title' => 'Raider'],
            ['name' => 'lawlop',              'icon' => 'crown', 'title' => 'Main Tank'],
            ['name' => 'LeviosaMimosa',       'icon' => 'crown', 'title' => 'Raider'],
            ['name' => 'Mapletree',           'icon' => 'crown', 'title' => 'Raider'],
            ['name' => 'Mashgar',             'icon' => 'crown', 'title' => 'Raider'],
            ['name' => 'mataglap',            'icon' => 'crown', 'title' => 'Raider'],
            ['name' => 'mattyp237',           'icon' => 'crown', 'title' => 'Parser'],
            ['name' => 'Mister Awesomesauce', 'icon' => 'crown', 'title' => 'Raider'],
            ['name' => 'Nectu',               'icon' => 'crown', 'title' => 'Main Tank'],
            ['name' => 'niph',                'icon' => 'crown', 'title' => 'Paypal'],
            ['name' => 'Nompire',             'icon' => 'crown', 'title' => 'Parser'],
            ['name' => 'Onehalf',             'icon' => 'crown', 'title' => 'Parser'],
            ['name' => 'Panya',               'icon' => 'crown', 'title' => 'Parser'],
            ['name' => 'Paul',                'icon' => 'crown', 'title' => 'Raider'],
            ['name' => 'Riotdog TV',          'icon' => 'crown', 'title' => 'Raider'],
            ['name' => 'Rishi',               'icon' => 'crown', 'title' => 'Parser'],
            ['name' => 'Rmalhada',            'icon' => 'crown', 'title' => 'Parser'],
            ['name' => 'ryooki',              'icon' => 'crown', 'title' => 'Parser'],
            ['name' => 'samspayde',           'icon' => 'crown', 'title' => 'Parser'],
            ['name' => 'Sangwa',              'icon' => 'crown', 'title' => 'Raider'],
            ['name' => 'Sarafina',            'icon' => 'crown', 'title' => 'Parser'],
            ['name' => 'SatanHimself',        'icon' => 'crown', 'title' => 'Raider'],
            ['name' => 'Sentence',            'icon' => 'crown', 'title' => 'Raider'],
            ['name' => 'SHIELD',              'icon' => 'crown', 'title' => 'Main Tank'],
            ['name' => 'skoz',                'icon' => 'crown', 'title' => 'Parser'],
            ['name' => 'Sleete',              'icon' => 'crown', 'title' => 'Raider'],
            ['name' => 'Slish',               'icon' => 'crown', 'title' => 'Parser'],
            ['name' => 'Strix',               'icon' => 'crown', 'title' => 'Black Lotus'],
            ['name' => 'Strken',              'icon' => 'crown', 'title' => 'Main Tank'],
            ['name' => 'Sumorex',             'icon' => 'crown', 'title' => 'Main Tank'],
            ['name' => 'Theseus#0373',        'icon' => 'crown', 'title' => 'Raider'],
            ['name' => 'TideAd',              'icon' => 'crown', 'title' => 'Parser'],
            ['name' => 'Tric',                'icon' => 'crown', 'title' => 'Main Tank'],
            ['name' => 'Triper',              'icon' => 'crown', 'title' => 'Parser'],
            ['name' => 'Tron',                'icon' => 'crown', 'title' => 'Main Tank'],
            ['name' => 'Uriah',               'icon' => 'crown', 'title' => 'Raider'],
            ['name' => 'Vejusatko',           'icon' => 'crown', 'title' => 'Raider'],
            ['name' => 'Vexxe#4040',          'icon' => 'crown', 'title' => 'Parser'],
            ['name' => 'Waughter',            'icon' => 'crown', 'title' => 'Raider'],
            ['name' => 'weirdGuy',            'icon' => 'crown', 'title' => 'Parser'],
            ['name' => 'Yjay',                'icon' => 'crown', 'title' => 'Raider'],
            ['name' => 'Zura',                'icon' => 'crown', 'title' => 'Parser'],
        ];

        $translators = [
            ['name' => 'Ardash',    'icon' => 'comment', 'title' => 'Russian + item lookups'],
            ['name' => 'ashrasmun', 'icon' => 'comment', 'title' => 'Polish'],
            ['name' => 'Etaya',     'icon' => 'comment', 'title' => 'German'],
            ['name' => 'Fingbel',   'icon' => 'comment', 'title' => 'French'],
            ['name' => 'Hopop',     'icon' => 'comment', 'title' => 'French'],
            ['name' => 'Irhala',    'icon' => 'comment', 'title' => 'French'],
            ['name' => 'Kayley',    'icon' => 'comment', 'title' => 'Danish'],
            ['name' => 'Kyraa',     'icon' => 'comment', 'title' => 'German'],
            ['name' => 'myki',      'icon' => 'comment', 'title' => 'French'],
            ['name' => 'Strix',     'icon' => 'comment', 'title' => 'Norwegian'],
        ];
    @endphp

    <div class="row bg-light pt-5 pb-5 mb-5 rounded">
        <div class="col-lg-4 offset-lg-2 col-md-5 offset-md-1 col-sm-6 col-12">
            <h1>
                <span class="fa fa-fw fas fa-heart text-danger"></span>
                {{ __("Donors") }}
            </h1>
            <ul class="fa-ul">
                @foreach ($donors as $donor)
                    <li>
                        <span class="fa-li fas fa-{{ $donor['icon'] }}"></span>
                        <span class="text-uncommon">{{ $donor['name'] }}</span>
                        <span class="text-muted">{{ $donor['title'] }}</span>
                    </li>
                @endforeach
                <li>
                    <span class="fa-li fas fa-question"></span>
                    {!! __("If I missed you, email me!") !!} <a href="mailto:lemmings19@gmail.com" target="_blank">lemmings19@gmail.com</a>
                </li>
            </ul>
        </div>
        <div class="col-lg-4 col-md-5 col-sm-6 col-12">
            <h1>
                <span class="fa fa-fw fas fa-heart text-danger"></span>
                {{ __("Translators") }}
            </h1>
            <ul class="fa-ul">
                @foreach ($translators as $translator)
                    <li>
                        <span class="fa-li fas fa-{{ $translator['icon'] }}"></span>
                        <span class="text-uncommon">{{ $translator['name'] }}</span>
                        <span class="text-muted">{{ $translator['title'] }}</span>
                    </li>
                @endforeach
                <li>
                    <span class="fa-li fas fa-question"></span>
                    {!! __("If I missed you, email me!") !!} <a href="mailto:lemmings19@gmail.com" target="_blank">lemmings19@gmail.com</a>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection
