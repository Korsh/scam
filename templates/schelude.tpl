<div>
    <div>    
        Mon <input type="checkbox" class="schelude_day" value="1">
        Tue <input type="checkbox" class="schelude_day" value="2">
        Wed <input type="checkbox" class="schelude_day" value="3">
        Thu <input type="checkbox" class="schelude_day" value="4">
        Fri <input type="checkbox" class="schelude_day" value="5">
        Sat <input type="checkbox" class="schelude_day" value="6">
        Sun <input type="checkbox" class="schelude_day" value="0">
    </div>
    <div>
        <p>
            Time interval:
            Minutes(0-59): <input class="clear small2" id="time_interval_minute" type="number" min="0" max="59" value="0">
            Hours(0-23): <input class="clear small2" id="time_interval_hour" type="number" min="0" max="23" value="2">
        </p>
        <p>
            Report after:
            Minutes(0-59): <input class="clear small2" id="report_interval_minute" type="number" min="0" max="59" value="0">
            Hours(0-23): <input class="clear small2" id="report_interval_hour" type="number" min="0" max="23" value="2">
        </p>
        
    </div>
    {include file="task_parameters.tpl"}
    <button name="save_task" id="save_task">Save</button>
</div>
