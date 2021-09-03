staticData = null;
perfData = null;
activityData = null;

function init(){
  staticData = get('static');
  perfData = get('performance');
  activityData = get('activity');
}

async function get(endpoint){
    const response = await fetch('https://darrenwu.xyz/api/' + endpoint + '/');
    const myJson = await response.json();
    return myJson;
}

window.addEventListener('DOMContentLoaded', init);