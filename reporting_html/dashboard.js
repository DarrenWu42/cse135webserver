import { data } from "../public_html/collector";

staticData = [];
perfData = [];
activityData = [];

start_times = [];
end_times = [];
durations = [];
languages = [];
connection_type = [];
user_agents = [];

cookied_sessions = 0;
total_sessions = 0;

colors=["#5165ff","#cb53e4","#ff4abd","#ff6094","#ff8870","#ffb35a","#ffdb5c","#f4ff7b"];

function parseDate(dt){
    var t = dt.split(/[- : \.]/);
    var d = new Date(Date.UTC(t[0],t[1]-1,t[2],t[3],t[4],t[5],t[6]/1000));
    return d.valueOf();
}

function parseData(){
    for(const static of staticData){
        if(static.cookies == "1")
            cookied_sessions++;

        user_agents.push(static.user_agent);
        languages.push(static.language);
        connection_types.push(static.effective_type);
    }

    for(const performance of performanceData){
        let start_time = parseDate(performance.fetch_start);
        start_times.push(start_time);

        let end_time = parseDate(performance.load_event_end);
        end_times.push(end_time);

        let duration = parseFloat(performance.duration) != 0 ? parseFloat(performance.duration) : end_time - start_time;
        durations.push(duration);
    }

    for(const activity of activityData){
        // do nothing for now
    }
}

var baseConfigPie = {
    type: 'pie',
    title: {
        text: '',
        align: 'center',
        offsetX: 10,
        fontSize: 25
    },
    valueBox: {
        placement: 'out',
        text: '%t\n%npv%',
        fontFamily: 'Open Sans'
    },
    legend: {
        backgroundColor: '#eee',
        fontSize: '1.5em',
        width: '150em'
    },
    tooltip: {
        fontSize: '18',
        fontFamily: 'Open Sans',
        padding: '5 10',
        text: '%npv%'
    },
    series:null
};

function loadTimesChart(){
    let loadTimesData = [{text:"<200ms",backgroundColor:"#00880B"},
                         {text:"200-500ms",backgroundColor:"#888500"},
                         {text:"500-1000ms",backgroundColor:"#d17719"},
                         {text:"1000+ms",backgroundColor:"#ff6969"}];
    let loadTimesValues = [0,0,0,0];
    for(const loadTime of durations){
        if(loadTime <= 200)
            loadTimesValues[0]++;
        else if(loadTime <= 500)
            loadTimesValues[1]++;
        else if(loadTime <= 1000)
            loadTimesValues[2]++;
        else
            loadTimesValues[3]++;
    }

    loadTimesValues = loadTimesValues.map(x => (x/durations.length)*100);

    for(var i = 0; i < loadTimesValues.length; i++)
        loadTimesData[i].values=loadTimesValues[i];
    
    baseConfigPie.series = loadTimesData;
    
    zingchart.render({
        id: 'loadTimesChart',
        data: baseConfigPie
    });
}

function connectionsChart(){
    let connectionsData = [{text:"Slow 2g",backgroundColor:"#ff6969"},
                         {text:"2g",backgroundColor:"#d17719"},
                         {text:"3g",backgroundColor:"#888500"},
                         {text:"4g",backgroundColor:"#00880B"}];
    let connectionsCounts = [0,0,0,0];
    for(const connection_type of connection_types){
        if(connection_type == "slow-2g")
            connectionsCounts[0]++;
        else if(connection_type == "2g")
            connectionsCounts[1]++;
        else if(connection_type == "3g")
            connectionsCounts[2]++;
        else
            connectionsCounts[3]++;
    }

    connectionsCounts = connectionsCounts.map(x => (x/connection_types.length)*100);

    for(var i = 0; i < connectionsCounts.length; i++)
        connectionsData[i].values=connectionsCounts[i];
    
    baseConfigPie.series = connectionsData;
    
    zingchart.render({
        id: 'connectionsChart',
        data: baseConfigPie
    });
}

function languageChart(){
    let languagesData = [];

    let languagesValues = [];
    let languagesCounts = [];
    for(const language of languages){
        
    }

    connectionsValues = connectionsValues.map(x => (x/connection_types.length)*100);

    for(var i = 0; i < connectionsValues.length; i++)
        connectionsData[i].values=connectionsValues[i];
    
    baseConfigPie.series = connectionsData;
    
    zingchart.render({
        id: 'connectionsChart',
        data: baseConfigPie
    });
}

async function get(endpoint){
    fetch('https://darrenwu.xyz/api/' + endpoint + '/').then(res => 
        res.json().then(data => ({
            data: data,
            status: res.status
        })
    ).then(res => {
        returnData = res.data;
        return data;
    }));
}

function initData(){
    staticData = get('static');
    perfData = get('performance');
    activityData = get('activity');
  
    total_sessions = staticData.length;

    parseData();

    loadTimesChart();
    connectionsChart();
    //languageChart();
    //agentChart();
    //
}

window.addEventListener('DOMContentLoaded', initData);