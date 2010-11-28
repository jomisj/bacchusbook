/*
////////////////////////////////////////////////////////////////////////////////
// ADOBE SYSTEMS INCORPORATED
// Copyright 2007 Adobe Systems Incorporated
// All Rights Reserved.
//
// NOTICE:  Adobe permits you to use, modify, and distribute this file in accordance with the 
// terms of the Adobe license agreement accompanying it.  If you have received this file from a 
// source other than Adobe, then your use, modification, or distribution of it requires the prior 
// written permission of Adobe.
////////////////////////////////////////////////////////////////////////////////
*/
import mx.charts.events.ChartItemEvent;
import mx.collections.ArrayCollection;
import mx.collections.XMLListCollection;
import mx.controls.Alert;
import mx.events.ItemClickEvent;
import mx.rpc.events.ResultEvent;


private var params:Object = new Object();
[Bindable]
private var listData:XMLListCollection; 
[Bindable]
private var dd1Data:XMLListCollection;

public function resultHandler(event:ResultEvent):void {
	var result:XML = XML(event.result);  
    var xmlList:XMLList = result.data.children();	
	listData = new XMLListCollection(xmlList); 
}

public function insertItemHandler(event:ResultEvent):void {	
	var result:int = parseInt(String(event.result));  
	if (result ==0){
		//clearInputFields();
		fill2();
	}
	else {
		Alert.show("Insert Failed!! Please retry.", "Alert Box", mx.controls.Alert.OK);
	}
}

public function WineServiceChart_resultHandler(event:ResultEvent):void
{
	var result:XML = XML(event.result);  
	var xmlList:XMLList = result.data.children();	
	listData = new XMLListCollection(xmlList); 
}

/*public function fill():void{
	wineService.removeEventListener(ResultEvent.RESULT,insertItemHandler);
	wineService.addEventListener(ResultEvent.RESULT,resultHandler);
	wineService.method = "GET";
	params['method'] = "FindAll";
	wineService.cancel();
	wineService.send(params);
	currentState='Page2';
}*/

public function chartByYear():void{
	WineServiceChart.addEventListener(ResultEvent.RESULT,WineServiceChart_resultHandler);
	WineServiceChart.method="GET";
	params['method'] = "chartGroupbyYear";
	WineServiceChart.cancel();
	WineServiceChart.send(params);
}

public function piechart1_itemClickHandler(event:ChartItemEvent):void{
	//Alert.show(event.hitData.item.year,"You Clicked..");
	wineService.removeEventListener(ResultEvent.RESULT,insertItemHandler);
	wineService.removeEventListener(ResultEvent.RESULT,WineServiceChart_resultHandler);
	wineService.addEventListener(ResultEvent.RESULT,resultHandler);
	wineService.method = "GET";
	params = {"method": "FindAllbyYear", "YearParm":parseInt(event.hitData.item.year)};
	wineService.cancel();
	wineService.send(params);
	currentState='Page2';
	
}

public function fill2():void{
	wineService.removeEventListener(ResultEvent.RESULT,insertItemHandler);
	wineService.removeEventListener(ResultEvent.RESULT,WineServiceChart_resultHandler);
	wineService.addEventListener(ResultEvent.RESULT,resultHandler);
	wineService.method = "GET";
	params['method'] = "FindAll2";
	params['YearParm'] = "0";
	wineService.cancel();
	wineService.send(params);
	currentState='Page2';
}
/*
public function insertWine():void{
	wineService.removeEventListener(ResultEvent.RESULT,resultHandler);
	wineService.addEventListener(ResultEvent.RESULT,insertItemHandler);
	wineService.method = "POST";
    params = {"method": "InsertWine", "WineID": NaN, "wine_name": inputWineName.text,
    			 "region": inputRegion.text, "grape_type": inputGrape.text,
				 "appelation": inputAppelation.text, "classification": inputClassification.text,
				 "color": inputColor.text, "year": inputYear.text,
				 "grading": inputGrading.text, "comments": inputComments.text}; 
	wineService.cancel();
	wineService.send(params);
}
*/
public function updateWine():void{
	wineService.removeEventListener(ResultEvent.RESULT,resultHandler);
	wineService.addEventListener(ResultEvent.RESULT,insertItemHandler);
	wineService.method = "POST";
	params = {"method": "updateWine", "WineID": dg.selectedItem.wineID, "wine_name": wine_name.text,
		"region": region.text, "grape_type": grape_type.text,
		"appelation": appelation.text, "classification": classification.text,
		"color": color.text, "year": year.text,
		"grading": grading.text, "comments": comments.text}; 
	wineService.cancel();
	wineService.send(params);
}

/*
private function clearInputFields():void{
	inputWineName.text = "";
	inputRegion.text = "";
	inputGrape.text = "";
	inputAppelation.text = "";
	inputClassification.text = "";
	inputColor.text = "";
	inputYear.text = "";
	inputGrading.text = "";
	inputComments.text = "";
}*/