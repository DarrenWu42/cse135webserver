staticData = [];
perfData = [];
activityData = [];

start_times = [];
end_times = [];
durations = [];
languages = [];
connection_types = [];
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

    for(const performance of perfData){
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
    plot:{
        detach: false
    },
    series:null
};

var baseConfigRing = {
    type: 'ring',
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
    plot:{
        detach: false
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

    for(var i = 0; i < loadTimesValues.length; i++){
        loadTimesData[i].values=[loadTimesValues[i]];
        if(loadTimesValues[i] == 0){
            loadTimesData.splice(i, 1);
            loadTimesValues.splice(i, 1);
            i--;
        }
    }
    
    baseConfigPie.series = loadTimesData;
    
    zingchart.render({
        id: 'loadTimesChart',
        data: baseConfigPie,
        height: '100%',
        width: '100%'
    });
}

function connectionsChart(){
    let connectionsData = [{text:"Slow 2g",backgroundColor:"#ff6969"},
                         {text:"2g",backgroundColor:"#d17719"},
                         {text:"3g",backgroundColor:"#888500"},
                         {text:"4g",backgroundColor:"#00880B"}];
    let connectionsValues = [0,0,0,0];
    for(const connection_type of connection_types){
        if(connection_type == "slow-2g")
            connectionsValues[0]++;
        else if(connection_type == "2g")
            connectionsValues[1]++;
        else if(connection_type == "3g")
            connectionsValues[2]++;
        else
            connectionsValues[3]++;
    }

    connectionsValues = connectionsValues.map(x => (x/connection_types.length)*100);

    for(var i = 0; i < connectionsValues.length; i++){
        connectionsData[i].values=[connectionsValues[i]];
        if(connectionsValues[i] == 0){
            connectionsData.splice(i, 1);
            connectionsValues.splice(i, 1);
            i--;
        }
    }
    
    baseConfigPie.series = connectionsData;
    
    zingchart.render({
        id: 'connectionsChart',
        data: baseConfigRing,
        height: '100%',
        width: '100%'
    });
}

function languagesChart(){
    let languagesData = [];

    let languagesValues = [];
    let languagesCounts = [];
    for(const language of languages){
        if(languagesValues.includes(language))
            languagesCounts[languagesValues.indexOf(language)]++;
        else{
            languagesValues.push(language);
            languagesCounts.push(1);
        }
    }

    languagesCounts = languagesCounts.map(x => (x/languages.length)*100);

    let lowerValue = languagesCounts.length < 7 ? languagesCounts.length : 7;
    
    for(var i = 0; i < lowerValue; i++){
        languagesData[i]={value:[languagesCounts[i]],
                          text:languagesValues[i],
                          backgroundColor:colors[i]};
    }
    
    baseConfigPie.series = languagesData;
    
    zingchart.render({
        id: 'languagesChart',
        data: baseConfigRing,
        height: '100%',
        width: '100%'
    });
}

function agentsChart(){
    let agentsData = [];

    let agentsValues = [];
    let agentsCounts = [];
    for(const agent of user_agents){
        if(agentsValues.includes(agent))
            agentsCounts[agentsValues.indexOf(agent)]++;
        else{
            agentsValues.push(agent);
            agentsCounts.push(1);
        }
    }

    agentsCounts = agentsCounts.map(x => (x/user_agents.length)*100);

    let lowerValue = agentsCounts.length < 7 ? agentsCounts.length : 7;

    for(var i = 0; i < lowerValue; i++){
        agentsData[i]={value:[agentsCounts[i]],
                       text:agentsValues[i],
                       backgroundColor:colors[i]};
    }
    
    baseConfigPie.series = agentsData;

    console.log(agentsData);
    
    zingchart.render({
        id: 'agentsChart',
        data: baseConfigPie,
        height: '100%',
        width: '100%'
    });
}

const get = async (endpoint) => {
    let request  = await fetch('https://reporting.darrenwu.xyz/api/' + endpoint);
    let jsonData = await request.json();
    return jsonData;
   };
   
async function initData(){
    staticData = await get('static');
    perfData = await get('performance');
    activityData = await get('activity');
  
    total_sessions = staticData.length;

    parseData();

    loadTimesChart();
    connectionsChart();
    languagesChart();
    agentsChart();
}

window.addEventListener('DOMContentLoaded', initData);