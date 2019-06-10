
/*
 Couleur par defaut, lorsque pas d'etat: blanc, sinon noir
 */
function colorState(state, defaultColor) {
    var colorState = "green";

    switch (state) {
        case "top": colorState="navy";
            break;
        case "OK": colorState="darkgreen";
            break;
        case "warn": colorState="orange";
            break;
        case "KO": colorState="red";
            break;
        case "?": colorState= (defaultColor=='w'?"white":"black");
            break;
    }
    return colorState;
}

function svgLoad(orientationState, small) {
    /*
     var orientationState = new Array();
     orientationState["n"]="OK";
     orientationState["nne"]="OK";
     */
    if (typeof orientationState !== 'undefined') {
        for (var name in orientationState) {
            var id=orientationIdFromName(name);
            $('#'+id).attr("fill", colorState(orientationState[name],'w'));
            $('#'+id+'Txt').attr("fill", colorState(orientationState[name],'b'));
        }
    }
}

function orientationIdFromName(name) {
    var result=name;
    switch (name) {
        case "n":
            result = "nord";
            break;
        case "nne":
            result = "nord-nord-est";
            break;
        case "ne":
            result = "nord-est";
            break;
        case "ene":
            result = "est-nord-est";
            break;
        case "e":
            result = "est";
            break;
        case "ese":
            result = "est-sud-est";
            break;
        case "se":
            result = "sud-est";
            break;
        case "sse":
            result = "sud-sud-est";
            break;
        case "s":
            result = "sud";
            break;
        case "ssw":
            result = "sud-sud-west";
            break;
        case "sw":
            result = "sud-west";
            break;
        case "wsw":
            result = "west-sud-west";
            break;
        case "w":
            result = "west";
            break;
        case "wnw":
            result = "west-nord-west";
            break;
        case "nw":
            result = "nord-west";
            break;
        case "nnw":
            result = "nord-nord-west";
            break;
    }
    return result;
}
