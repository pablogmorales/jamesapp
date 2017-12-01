window.resolveReference=function(parent,ref,cache){if(cache=cache||{},cache[ref])return cache[ref];var keys=ref.split("/");keys.shift();var cur=parent;return keys.forEach(function(k){cur=cur[k]}),cache[ref]=cur},window.dereference=function(parent,obj,cache){if(void 0===obj&&(obj=parent),cache=cache||{},"object"!=typeof obj)return obj;if(Array.isArray(obj)){for(var i=0;i<obj.length;++i)obj[i]=dereference(parent,obj[i],cache);return obj}for(var key in obj){var val=obj[key];if("$ref"===key)return resolveReference(parent,val,cache);obj[key]=dereference(parent,val,cache)}return obj};var X_VALUES=["dynamicEnum","dynamicValue","hidden","consoleDefault","inputType","group"];window.parseSwagger=function(swagger,deref){var fixParameter=function(param){return param.$ref&&(param=resolveReference(swagger,param.$ref)),param.schema&&param.schema.$ref&&(param.schema=resolveReference(swagger,param.schema.$ref)),X_VALUES.forEach(function(v){param[v]=param[v]||param["x-"+v]}),param},fixOperation=function(op,pathParams){op.responses=op.responses||{},op.parameters=(op.parameters||[]).concat(pathParams||[]),op.parameters=op.parameters.map(fixParameter);var successResponse=op.responses[200]=op.responses[200]||{};"No response was specified"===successResponse.description&&(successResponse.description=""),successResponse.description||(successResponse.description="OK")};deref&&(swagger=dereference(swagger));for(var path in swagger.paths){var pathParams=swagger.paths[path].parameters||[];for(var method in swagger.paths[path])fixOperation(swagger.paths[path][method],pathParams)}return swagger};var EXAMPLES={};EXAMPLES.parameterExample=function(param,path){var ret="";if(param.example)ret=param.example;else if("date"===param.format)ret="1987-09-23";else if("date-time"===param.format)ret="1987-09-23T18:30:00Z";else if("integer"===param.type)ret="123";else if("number"===param.type)ret="1.23";else if("string"===param.type)ret="xyz";else if("boolean"===param.type)ret="true";else if("array"===param.type){var choices=[];param["enum"]?choices=param["enum"].filter(function(choice,idx){return 2>idx}):param.items&&(choices="string"===param.items.type?["foo","bar"]:"integer"===param.items.type?["1","2","3"]:"number"===param.items.type?["1.0","2.0","3.0"]:"boolean"===param.items.type?["true","false"]:[parameterExample(param.items,path)]),ret="csv"===param.collectionFormat?choices.join(","):"ssv"===param.collectionFormat?choices.join(" "):"tsv"===param.collectionFormat?choices.join("\\t"):"pipes"===param.collectionFormat?choices.join("|"):"multi"===param.collectionFormat?choices[0]:choices.join()}return param["enum"]&&"array"!==param.type&&(ret=param["enum"][0]),"path"===param["in"]?ret=path.replace("{"+param.name+"}",ret):"query"===param["in"]?ret="?"+param.name+"="+ret:"formData"===param["in"]?ret=param.name+"="+ret:"header"===param["in"]&&(ret=param.name+": "+ret),ret},EXAMPLES.schemaExample=function(schema,readable){if(!schema)return"";if("array"===schema.type)return[EXAMPLES.schemaExample(schema.items,readable)];if("object"===schema.type||schema.properties){var ret={};if(schema.properties)for(key in schema.properties)ret[key]=EXAMPLES.schemaExample(schema.properties[key],readable);if(schema.additionalProperties){var example=EXAMPLES.schemaExample(schema.additionalProperties,readable);ret.item1=example,ret.item2=example}return(schema.allOf||[]).forEach(function(def){for(var key in def.properties)ret[key]=EXAMPLES.schemaExample(def.properties[key],readable)}),ret}return"integer"===schema.type?readable?123:0:"number"===schema.type?readable?1.23:0:"string"===schema.type?readable?"xyz":"string":"boolean"===schema.type?!0:void 0};var getSeparatorFromFormat=function(format){return format&&"csv"!==format&&"multi"!==format?"tsv"===format?"	":"ssv"===format?" ":"pipes"===format?"|":void 0:","};window.swaggerRequest=function(opts){var req={},swagger=opts.swagger,keys=opts.keys=opts.keys||{},answers=opts.answers=opts.answers||{},op=swagger.paths[opts.path][opts.method];req.protocol=-1===swagger.schemes.indexOf("https")?swagger.schemes[0]:"https",req.domain=swagger.host,req.method=opts.method;var basePath=swagger.basePath;basePath.lastIndexOf("/")===basePath.length-1&&(basePath=basePath.substring(0,basePath.length-1)),req.path=basePath+opts.path;var addParam=function(parameter,answer){"file"===parameter.type?(req.files=req.files||{},req.files[parameter.name]=answer):"path"===parameter["in"]?req.path=req.path.replace("{"+parameter.name+"}",answer):"header"===parameter["in"]?(req.headers=req.headers||{},req.headers[parameter.name]=answer):"formData"===parameter["in"]?(req.body?req.body+="&":req.body="",req.body+=parameter.name+"="+encodeURIComponent(answer)):"query"===parameter["in"]?(req.query=req.query||{},req.query[parameter.name]=answer):"body"===parameter["in"]&&(req.body=JSON.parse(answer))};if(op.parameters.forEach(function(parameter){var answer=answers[parameter.name];if(void 0===answer||""===answer)if("path"===parameter["in"])answer="";else{if("file"!==parameter.type)return;var fileInput=$('input[name="'+parameter.name+'"]')[0];if(!fileInput||!fileInput.files||!fileInput.files.length)return;answer=fileInput.files[0]}if("array"===parameter.type){answer=answer||[];var sep=getSeparatorFromFormat(parameter.collectionFormat);sep&&(answer=answer.join(sep))}addParam(parameter,answer)}),swagger.securityDefinitions){var addedOauth=!1;for(var sec in swagger.securityDefinitions)sec=swagger.securityDefinitions[sec],"apiKey"===sec.type?keys[sec.name]&&addParam(sec,keys[sec.name]):"oauth2"===sec.type&&keys.oauth2&&!addedOauth?("implicit"===sec.flow?addParam({"in":"query",name:"access_token"},keys.oauth2):addParam({"in":"header",name:"Authorization"},"Bearer "+keys.oauth2),addedOauth=!0):"basic"===sec.type&&keys.username&&keys.password&&addParam({"in":"header",name:"Authorization"},"Basic "+btoa(keys.username+":"+keys.password))}return req},window.ajaxRequest=function(opts){var req=swaggerRequest(opts),ajaxReq={url:req.protocol+"://"+req.domain+req.path,method:req.method.toUpperCase(),headers:req.headers,data:req.body};if(opts.proxy&&(ajaxReq.url=opts.proxy+"/proxy/"+req.protocol+"/"+req.domain+req.path),"object"==typeof ajaxReq.data&&(ajaxReq.data=JSON.stringify(ajaxReq.data),ajaxReq.headers=ajaxReq.headers||{},ajaxReq.headers["Content-Type"]="application/json"),req.query&&(ajaxReq.url+="?",Object.keys(req.query).forEach(function(k,idx){0!==idx&&(ajaxReq.url+="&"),ajaxReq.url+=k+"="+encodeURIComponent(req.query[k])})),req.files){ajaxReq.data=new FormData;for(var pname in req.files)ajaxReq.data.append(pname,req.files[pname]);ajaxReq.cache=ajaxReq.contentType=ajaxReq.processData=!1}return ajaxReq},App.controller("Swagger",function($scope){var METHOD_ORDER=["def","get","post","patch","put","delete"],sortOperations=function(a,b){var ret=METHOD_ORDER.indexOf(a.method)-METHOD_ORDER.indexOf(b.method);if(ret)return ret;var compA=a.path||a.definition,compB=b.path||b.definition;return compA>compB?1:compB>compA?-1:0};$scope.swagger=window.parseSwagger(window.SWAGGER),$scope.operations=[];for(var path in $scope.swagger.paths)for(var method in $scope.swagger.paths[path])if("parameters"!==method){var op=$scope.swagger.paths[path][method];op.path=path,op.method=method,$scope.operations.push(op)}$scope.operations.sort(sortOperations),$scope.$watch("operations.active",function(){$scope.operation=$scope.operations.active}),$scope.stripHtml=function(str){return str?str.replace(/<(?:.|\n)*?>/gm,""):str}});var followPath=function(obj,path){if(!path||!obj)return obj;var dot=path.indexOf(".");if(-1===dot)return obj[path];var key=path.substring(0,dot);return followPath(obj[key],path.substring(dot+1))};App.controller("Parameters",function($scope){$scope.expanded=$scope.operations.active&&$scope.operations.active.parameters.length<5}),App.controller("Parameter",function($scope){function getBestInputType(){if($scope.parameter.inputType)return $scope.parameter.inputType;var type=$scope.parameter.type;return"body"===$scope.parameter["in"]?"body":"array"===type?$scope.parameter["enum"]?"checkboxes":"dynamicArray":$scope.parameter["enum"]||$scope.parameter.dynamicEnum?"dropdown":"number"===type||"integer"===type?"number":"file"===type?"file":"text"}function getFromAPI(dyn,cb){var answers={},allAnswers=$scope.getAnswers();(dyn.parameters||[]).forEach(function(p){p.value.answer?answers[p.name]=allAnswers[p.value.answer]:answers[p.name]=p.value});for(var key in ENV.embedParameters||{})answers[key]=ENV.embedParameters[key];var req=window.ajaxRequest({swagger:$scope.swagger,path:dyn.path,method:dyn.method,answers:answers,keys:$scope.keys});$.ajax(req).done(function(data){if(!window.checkResponse)return cb(data);var resp=window.checkResponse(data);return"success"===resp.type?cb(data):($scope.error=resp.message,void cb())}).fail(function(xhr){$scope.error=xhr.responseText,cb()}).always(function(){$scope.$apply()})}function setVal(data){$scope.model[$scope.parameter.name]=$scope.parameter.consoleDefault=followPath(data,dynVal.value)}for(var key in $scope.parameter)0==key.indexOf("x-")&&($scope.parameter[key.substring(2)]=$scope.parameter[key]);var name=$scope.parameter.name;$scope.model||($scope.keys&&name in $scope.keys?$scope.model=$scope.keys:$scope.parameter.global?$scope.model=$scope.globalAnswers:$scope.model=$scope.answers),void 0===$scope.model[name]&&($scope.model[name]=$scope.parameter.consoleDefault||$scope.parameter["default"]),$scope.$watch("parameter.type",function(){$scope.inputType=getBestInputType()});var getFromStep=function(dyn){if(void 0!==dyn.fromStep){var step=$scope.recipe.steps[dyn.fromStep];if(dyn.answer)return step.answers[dyn.answer];var op=$scope.swagger.paths[step.apiCall.path][step.apiCall.method];return op.response?op.response:void($scope.error="Please complete step "+(dyn.fromStep+1)+" first.")}},dynVal=$scope.parameter.dynamicValue;dynVal&&(void 0!==dynVal.fromStep?setVal(getFromStep(dynVal)):getFromAPI(dynVal,setVal));var dynEnum=$scope.parameter.dynamicEnum;dynEnum&&($scope.refreshEnum=function(){function setEnum(data){if(data){var arr=followPath(data,dynEnum.array);$scope.parameter["enum"]=arr.map(function(choice){return followPath(choice,dynEnum.value)}),$scope.parameter.enumLabels=arr.map(function(choice){return followPath(choice,dynEnum.label)}),$scope.refreshingEnum=!1}}$scope.error=null,$scope.refreshingEnum=!0,void 0!==dynEnum.fromStep?setEnum(getFromStep(dynEnum)):getFromAPI(dynEnum,setEnum)})}),App.controller("Checkboxes",function($scope){$scope.chosen={};var defaults=$scope.model[$scope.parameter.name];defaults&&defaults.forEach(function(d){$scope.chosen[d]=!0}),$scope.$watch("chosen",function(){var values=Object.keys($scope.chosen).filter(function(k){return $scope.chosen[k]});$scope.model[$scope.parameter.name]=values},!0)}),App.controller("DynamicArray",function($scope){$scope.items=[],$scope.addItem=function(){$scope.items.push({})},$scope.removeItem=function(index){$scope.items=$scope.items.filter(function(item,i){return i!==index})},$scope.$watch("items",function(){$scope.model[$scope.parameter.name]=$scope.items.map(function(item){return item.value})},!0)}),App.controller("BodyInput",function($scope){$scope.fields={},$scope.inputAs="fields",$scope.rawFieldTypes=["string","integer","number"],$scope.isRawField=function(t){return-1!==$scope.rawFieldTypes.indexOf(t)},$scope.$watch("fields",function(){var bodyObj=JSON.parse(JSON.stringify($scope.fields));for(key in $scope.parameter.schema.properties){var schema=$scope.parameter.schema.properties[key];if(!$scope.isRawField(schema.type)&&bodyObj[key])try{bodyObj[key]=JSON.parse(bodyObj[key])}catch(e){var msg="Error parsing JSON field "+key+":"+e.toString();return void($scope.bodyParseError=msg)}}$scope.bodyParseError=null,$scope.bodyString=JSON.stringify(bodyObj,null,2),$scope.model[$scope.parameter.name]=JSON.stringify(bodyObj)},!0),$scope.$watch("bodyString",function(){try{$scope.fields=JSON.parse($scope.bodyString);for(key in $scope.parameter.schema.properties){var schema=$scope.parameter.schema.properties[key];$scope.isRawField(schema.type)||($scope.fields[key]=JSON.stringify($scope.fields[key],null,2))}$scope.bodyParseError=null}catch(e){$scope.bodyParseError=e.toString()}})}),App.controller("DateTime",function($scope){var val=$scope.model[$scope.parameter.name];val&&($scope.datetime=new Date(val)),$scope.$watch("datetime",function(){$scope.datetime&&($scope.model[$scope.parameter.name]=$scope.datetime.getTime())})});