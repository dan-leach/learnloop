<footer class="bg-info">
    <ul class="nav justify-content-center">
        <li class="nav-item">
            <a class="nav-link" href="/">LearnLoop v3.1</a>
          </li>
        <li class="nav-item">
          <a class="nav-link" href="https://danleach.uk">Created by Dan Leach</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="mailto:mail@learnloop.co.uk">Contact: mail@learnloop.co.uk</a>
        </li>
        <li>
            <a class="nav-link" href="/privacy-policy/">Privacy policy</a>
        </li>
        <li>
            <a class="nav-link" href="#" v-on:click="hideComponent('all'); showComponent('attendanceReport'); window.history.replaceState('https://learnloop.co.uk', 'LearnLoop', '/?attendance=')">Attendance report</a>
        </li>
        <br>
    </ul>
</footer>