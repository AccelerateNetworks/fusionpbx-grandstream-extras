<?php

function app_name() {
    return "";
}

function app_render() { ?>
<?xml version="1.0" encoding="UTF-8"?>
<xmlapp title="App Not Configured">
    <view>
        <section>
            <text label="apps are not configured for this device. please contact your phone administrator" />
        </section>
    </view>
    <Softkeys>
        <Softkey action="QuitApp" label="Exit" />
    </Softkeys>
</xmlapp>
<?php } ?>
