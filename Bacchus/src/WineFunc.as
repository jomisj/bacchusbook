
import mx.charts.events.ChartItemEvent;
import mx.collections.ArrayCollection;
import mx.collections.XMLListCollection;
import mx.controls.Alert;
import mx.events.ItemClickEvent;
import mx.rpc.events.ResultEvent;

[Bindable]
private var listData:XMLListCollection; 
[Bindable]
private var dd1Data:XMLListCollection;
[Bindable]
private var wineList:XMLListCollection;
[Bindable]
private var locationList:XMLListCollection;
[Bindable]

private var rackList:XMLListCollection;
[Bindable]
private var rowList:XMLListCollection;
[Bindable]
private var colList:XMLListCollection;

public var removeRC:Boolean;
public var removalComments:String;
public var arrWineDetail:Array;

public function resultHandler(event:ResultEvent):void {
	var result:XML = XML(event.result);  
    var xmlList:XMLList = result.data.children();	
	listData = new XMLListCollection(xmlList); 
}

public function insertItemHandler(event:ResultEvent):void {	
	var result:int = parseInt(String(event.result));  
	if (result ==0){
		Alert.show("Wine Added Sucessfully", "Info"); 
		clearWineAddFields();
		loadWineList();
		currentState='Page3';
	}
	else {
		Alert.show("Insert Failed!! Please retry.", "Alert Box", mx.controls.Alert.OK);
	}
}

public function removeItemHandler(event:ResultEvent):void {	
	var result:int = parseInt(String(event.result));  
	if (result ==0){
		fill2();
	}
	else {
		Alert.show("Remove Failed!! Please retry.", "Alert Box", mx.controls.Alert.OK);
	}
}

public function updateItemHandler(event:ResultEvent):void {	
	var result:int = parseInt(String(event.result));
	if (result ==0){
		fill2();
	}
	else {
		Alert.show("Update Failed!! Please retry.", "Alert Box", mx.controls.Alert.OK);
	}
}

public function addAnotherItemHandler(event:ResultEvent):void {
	var result:int = parseInt(String(event.result));
	if (result ==0){
		fill2();
	}
	else {
		Alert.show("Adding another wine Failed!! Please retry.", "Alert Box", mx.controls.Alert.OK);
	}
}

public function WineServiceChart_resultHandler(event:ResultEvent):void
{
	var result:XML = XML(event.result);  
	var xmlList:XMLList = result.data.children();	
	listData = new XMLListCollection(xmlList); 
}

public function loadWineListHandler(event:ResultEvent):void {
	var result:XML = XML(event.result);  
	var xmlList:XMLList = result.data.children();
	var emptyXmlList:XMLList = new XMLList;
	wineList = new XMLListCollection;
	wineList = new XMLListCollection(xmlList); 
}

public function loadEmptyLocationHandler(event:ResultEvent):void {
	var result:XML = XML(event.result);  
	var xmlList:XMLList = result.data.children();
	var emptyXmlList:XMLList = new XMLList;
	locationList = new XMLListCollection;
	locationList = new XMLListCollection(xmlList); 
}
public function loadRackListHandler(event:ResultEvent):void {
	var result:XML = XML(event.result);  
	var xmlList:XMLList = result.data.children();
	var emptyXmlList:XMLList = new XMLList;
	rackList = new XMLListCollection;
	rackList = new XMLListCollection(xmlList); 
}
public function loadRowListHandler(event:ResultEvent):void {
	var result:XML = XML(event.result);  
	var xmlList:XMLList = result.data.children();
	var emptyXmlList:XMLList = new XMLList;
	rowList = new XMLListCollection;
	rowList = new XMLListCollection(xmlList); 
}
public function loadColListHandler(event:ResultEvent):void {
	var result:XML = XML(event.result);  
	var xmlList:XMLList = result.data.children();
	var emptyXmlList:XMLList = new XMLList;
	colList = new XMLListCollection;
	colList = new XMLListCollection(xmlList); 
}


public function chartByYear():void{
	var params:Object = new Object();
	WineServiceChart.addEventListener(ResultEvent.RESULT,WineServiceChart_resultHandler);
	WineServiceChart.method="GET";
	params['method'] = "chartGroupbyYear";
	WineServiceChart.cancel();
	WineServiceChart.send(params);
}

public function piechart1_itemClickHandler(event:ChartItemEvent):void{
	var params:Object = new Object();
	wineService.removeEventListener(ResultEvent.RESULT,insertItemHandler);
	wineService.removeEventListener(ResultEvent.RESULT,WineServiceChart_resultHandler);
	wineService.addEventListener(ResultEvent.RESULT,resultHandler);
	wineService.method = "GET";
	params = {"method": "FindAllbyYear", "YearParm":parseInt(event.hitData.item.year)};
	wineService.cancel();
	wineService.send(params);
	currentState='Page2';
	
}

public function loadWineList():void{
	var params:Object = new Object();
	wineService.removeEventListener(ResultEvent.RESULT,insertItemHandler);
	wineService.removeEventListener(ResultEvent.RESULT,updateItemHandler);
	wineService.removeEventListener(ResultEvent.RESULT,removeItemHandler);
	wineService.removeEventListener(ResultEvent.RESULT,addAnotherItemHandler);
	wineService.removeEventListener(ResultEvent.RESULT,resultHandler);
	wineService.addEventListener(ResultEvent.RESULT,loadWineListHandler);
	wineService.method = "GET";
	params['method'] = "loadWineList";
	wineService.cancel();
	wineService.send(params);
}

public function loadLocationList():void{
	var params:Object = new Object();
	wineService.removeEventListener(ResultEvent.RESULT,insertItemHandler);
	wineService.removeEventListener(ResultEvent.RESULT,updateItemHandler);
	wineService.removeEventListener(ResultEvent.RESULT,removeItemHandler);
	wineService.removeEventListener(ResultEvent.RESULT,addAnotherItemHandler);
	wineService.removeEventListener(ResultEvent.RESULT,resultHandler);
	wineService.removeEventListener(ResultEvent.RESULT,WineServiceChart_resultHandler);
	wineService.removeEventListener(ResultEvent.RESULT,loadWineListHandler);
	wineService.removeEventListener(ResultEvent.RESULT,loadRackListHandler);
	wineService.addEventListener(ResultEvent.RESULT,loadEmptyLocationHandler);
	wineService.method = "GET";
	params['method'] = "loadLocationList";
	wineService.cancel();
	wineService.send(params);
}

public function loadRackList():void{
	var params:Object = new Object();
	wineService.removeEventListener(ResultEvent.RESULT,insertItemHandler);
	wineService.removeEventListener(ResultEvent.RESULT,updateItemHandler);
	wineService.removeEventListener(ResultEvent.RESULT,removeItemHandler);
	wineService.removeEventListener(ResultEvent.RESULT,WineServiceChart_resultHandler);
	wineService.removeEventListener(ResultEvent.RESULT,addAnotherItemHandler);
	wineService.removeEventListener(ResultEvent.RESULT,resultHandler);
	wineService.removeEventListener(ResultEvent.RESULT,loadWineListHandler);
	wineService.removeEventListener(ResultEvent.RESULT,loadEmptyLocationHandler);
	wineService.removeEventListener(ResultEvent.RESULT,loadColListHandler);
	wineService.removeEventListener(ResultEvent.RESULT,loadRowListHandler);
	wineService.addEventListener(ResultEvent.RESULT,loadRackListHandler);
	wineService.method = "GET";
	params['method'] = "loadRackList";
	wineService.cancel();
	wineService.send(params);
}
public function loadRowList():void{
	var params:Object = new Object();
	wineService.removeEventListener(ResultEvent.RESULT,insertItemHandler);
	wineService.removeEventListener(ResultEvent.RESULT,updateItemHandler);
	wineService.removeEventListener(ResultEvent.RESULT,removeItemHandler);
	wineService.removeEventListener(ResultEvent.RESULT,addAnotherItemHandler);
	wineService.removeEventListener(ResultEvent.RESULT,WineServiceChart_resultHandler);
	wineService.removeEventListener(ResultEvent.RESULT,resultHandler);
	wineService.removeEventListener(ResultEvent.RESULT,loadWineListHandler);
	wineService.removeEventListener(ResultEvent.RESULT,loadEmptyLocationHandler);
	wineService.removeEventListener(ResultEvent.RESULT,loadColListHandler);
	wineService.removeEventListener(ResultEvent.RESULT,loadRackListHandler);
	wineService.addEventListener(ResultEvent.RESULT,loadRowListHandler);
	wineService.method = "GET";
	params['method'] = "loadRowList";
	params['rack'] = parseInt(rack.selectedItem.rackID);
	wineService.cancel();
	wineService.send(params);
}
public function loadColList():void{
	var params:Object = new Object();
	wineService.removeEventListener(ResultEvent.RESULT,insertItemHandler);
	wineService.removeEventListener(ResultEvent.RESULT,updateItemHandler);
	wineService.removeEventListener(ResultEvent.RESULT,removeItemHandler);
	wineService.removeEventListener(ResultEvent.RESULT,addAnotherItemHandler);
	wineService.removeEventListener(ResultEvent.RESULT,resultHandler);
	wineService.removeEventListener(ResultEvent.RESULT,loadWineListHandler);
	wineService.removeEventListener(ResultEvent.RESULT,loadEmptyLocationHandler);
	wineService.removeEventListener(ResultEvent.RESULT,WineServiceChart_resultHandler);
	wineService.removeEventListener(ResultEvent.RESULT,loadRowListHandler);
	wineService.removeEventListener(ResultEvent.RESULT,loadRackListHandler);
	wineService.addEventListener(ResultEvent.RESULT,loadColListHandler);
	wineService.method = "GET";
	params['method'] = "loadColList";
	params['rack'] = parseInt(rack.selectedItem.rackID);
	params['row'] = parseInt(row.selectedItem.row);
	wineService.cancel();
	wineService.send(params);
}

public function searchForRed():void{
	var params:Object = new Object();
	wineService.removeEventListener(ResultEvent.RESULT,insertItemHandler);
	wineService.removeEventListener(ResultEvent.RESULT,updateItemHandler);
	wineService.removeEventListener(ResultEvent.RESULT,removeItemHandler);
	wineService.removeEventListener(ResultEvent.RESULT,addAnotherItemHandler);
	wineService.removeEventListener(ResultEvent.RESULT,WineServiceChart_resultHandler);
	wineService.removeEventListener(ResultEvent.RESULT,loadWineListHandler);
	wineService.addEventListener(ResultEvent.RESULT,resultHandler);
	wineService.method = "GET";
	params['method'] = "searchByColor";
	params['color'] = "red";
	params['YearParm']="0";
	wineService.cancel();
	wineService.send(params);
	currentState='Page2';
}

public function searchForRose():void{
	var params:Object = new Object();
	wineService.removeEventListener(ResultEvent.RESULT,insertItemHandler);
	wineService.removeEventListener(ResultEvent.RESULT,updateItemHandler);
	wineService.removeEventListener(ResultEvent.RESULT,removeItemHandler);
	wineService.removeEventListener(ResultEvent.RESULT,addAnotherItemHandler);
	wineService.removeEventListener(ResultEvent.RESULT,WineServiceChart_resultHandler);
	wineService.removeEventListener(ResultEvent.RESULT,loadWineListHandler);
	wineService.addEventListener(ResultEvent.RESULT,resultHandler);
	wineService.method = "GET";
	params['method'] = "searchByColor";
	params['color'] = "rose";
	params['YearParm']="0";
	wineService.cancel();
	wineService.send(params);
	currentState='Page2';
}

public function searchForWhite():void{
	var params:Object = new Object();
	wineService.removeEventListener(ResultEvent.RESULT,insertItemHandler);
	wineService.removeEventListener(ResultEvent.RESULT,updateItemHandler);
	wineService.removeEventListener(ResultEvent.RESULT,removeItemHandler);
	wineService.removeEventListener(ResultEvent.RESULT,addAnotherItemHandler);
	wineService.removeEventListener(ResultEvent.RESULT,WineServiceChart_resultHandler);
	wineService.removeEventListener(ResultEvent.RESULT,loadWineListHandler);
	wineService.addEventListener(ResultEvent.RESULT,resultHandler);
	wineService.method = "GET";
	params['method'] = "searchByColor";
	params['color'] = "white";
	params['YearParm']="0";
	wineService.cancel();
	wineService.send(params);
	currentState='Page2';
}



public function searchForYoung():void{
	var params:Object = new Object();
	wineService.removeEventListener(ResultEvent.RESULT,insertItemHandler);
	wineService.removeEventListener(ResultEvent.RESULT,updateItemHandler);
	wineService.removeEventListener(ResultEvent.RESULT,removeItemHandler);
	wineService.removeEventListener(ResultEvent.RESULT,addAnotherItemHandler);
	wineService.removeEventListener(ResultEvent.RESULT,WineServiceChart_resultHandler);
	wineService.removeEventListener(ResultEvent.RESULT,loadWineListHandler);
	wineService.addEventListener(ResultEvent.RESULT,resultHandler);
	wineService.method = "GET";
	params['method'] = "searchByAge";
	params['age'] = "young";
	params['YearParm']="0";
	wineService.cancel();
	wineService.send(params);
	currentState='Page2';
}

public function searchForPrime():void{
	var params:Object = new Object();
	wineService.removeEventListener(ResultEvent.RESULT,insertItemHandler);
	wineService.removeEventListener(ResultEvent.RESULT,updateItemHandler);
	wineService.removeEventListener(ResultEvent.RESULT,removeItemHandler);
	wineService.removeEventListener(ResultEvent.RESULT,addAnotherItemHandler);
	wineService.removeEventListener(ResultEvent.RESULT,WineServiceChart_resultHandler);
	wineService.removeEventListener(ResultEvent.RESULT,loadWineListHandler);
	wineService.addEventListener(ResultEvent.RESULT,resultHandler);
	wineService.method = "GET";
	params['method'] = "searchByAge";
	params['age'] = "prime";
	params['YearParm']="0";
	wineService.cancel();
	wineService.send(params);
	currentState='Page2';
}

public function searchForOld():void{
	var params:Object = new Object();
	wineService.removeEventListener(ResultEvent.RESULT,insertItemHandler);
	wineService.removeEventListener(ResultEvent.RESULT,updateItemHandler);
	wineService.removeEventListener(ResultEvent.RESULT,removeItemHandler);
	wineService.removeEventListener(ResultEvent.RESULT,addAnotherItemHandler);
	wineService.removeEventListener(ResultEvent.RESULT,WineServiceChart_resultHandler);
	wineService.removeEventListener(ResultEvent.RESULT,loadWineListHandler);
	wineService.addEventListener(ResultEvent.RESULT,resultHandler);
	wineService.method = "GET";
	params['method'] = "searchByAge";
	params['age'] = "old";
	params['YearParm']="0";
	wineService.cancel();
	wineService.send(params);
	currentState='Page2';
}

public function fill2():void{
	var params:Object = new Object();
	wineService.removeEventListener(ResultEvent.RESULT,insertItemHandler);
	wineService.removeEventListener(ResultEvent.RESULT,updateItemHandler);
	wineService.removeEventListener(ResultEvent.RESULT,removeItemHandler);
	wineService.removeEventListener(ResultEvent.RESULT,addAnotherItemHandler);
	wineService.removeEventListener(ResultEvent.RESULT,WineServiceChart_resultHandler);
	wineService.removeEventListener(ResultEvent.RESULT,loadWineListHandler);
	wineService.addEventListener(ResultEvent.RESULT,resultHandler);
	wineService.method = "GET";
	params['method'] = "FindAll2";
	params['YearParm']="0";
	wineService.cancel();
	wineService.send(params);
	currentState='Page2';
}



public function removeBottle():void{
	Alert.show("I selected remove","");
	var params:Object = new Object();
	wineService.removeEventListener(ResultEvent.RESULT,insertItemHandler);
	wineService.removeEventListener(ResultEvent.RESULT,updateItemHandler);
	wineService.removeEventListener(ResultEvent.RESULT,addAnotherItemHandler);
	wineService.removeEventListener(ResultEvent.RESULT,loadWineListHandler);
	wineService.addEventListener(ResultEvent.RESULT,removeItemHandler);
	wineService.method = "GET";
	params['method'] = "removeBottle";
	params['bottleID'] = parseInt(dg.selectedItem.bottleID);
	params['wineID']= parseInt(dg.selectedItem.wineID);
	params['comments'] = removalComments;
	wineService.cancel();
	wineService.send(params);
}

public function AddAnotherBottle():void {
	var params:Object = new Object();
	wineService.removeEventListener(ResultEvent.RESULT,resultHandler);
	wineService.removeEventListener(ResultEvent.RESULT,insertItemHandler);
	wineService.removeEventListener(ResultEvent.RESULT,updateItemHandler);
	wineService.removeEventListener(ResultEvent.RESULT,loadWineListHandler);
	wineService.addEventListener(ResultEvent.RESULT,addAnotherItemHandler);
	wineService.method = "POST";
	params = {"method": "addAnotherBottle", "wineID": parseInt(dg.selectedItem.wineID), "wine_name": wine_name.text,
		"region": region.text, "grape_type": grape_type.text,
		"appelation": appelation.text, "classification": classification.text,
		"color": color.text, "year": year.text,
		"grading": grading.text, "comments": comments.text,
		"bottleID" : parseInt(dg.selectedItem.bottleID), 
		"price": price.text, "bottle_size": bottle_size.text,
		"purchase_date": purchase_date.text, "removal_date": removal_date.text,
		"eventID":parseInt(dg.selectedItem.eventID), "event_comment": event_comment.text,
		"drink_start": drink_start.text, "drink_end": drink_end.text,
		"best_start": best_start.text, "best_end": best_end.text
	}; 
	wineService.cancel();
	wineService.send(params);	
}

public function updateWine():void{
	var params:Object = new Object();
	wineService.removeEventListener(ResultEvent.RESULT,resultHandler);
	wineService.removeEventListener(ResultEvent.RESULT,insertItemHandler);
	wineService.removeEventListener(ResultEvent.RESULT,loadWineListHandler);
	wineService.addEventListener(ResultEvent.RESULT,updateItemHandler);
	wineService.method = "POST";
	params = {"method": "updateWine", "wineID": parseInt(dg.selectedItem.wineID), "wine_name": wine_name.text,
		"region": region.text, "grape_type": grape_type.text,
		"appelation": appelation.text, "classification": classification.text,
		"color": color.text, "year": year.text,
		"grading": grading.text, "comments": comments.text,
		"bottleID" : parseInt(dg.selectedItem.bottleID), 
		"price": price.text, "bottle_size": bottle_size.text,
		"purchase_date": purchase_date.text, "removal_date": removal_date.text,
		"eventID":parseInt(dg.selectedItem.eventID), "event_comment": event_comment.text,
		"drink_start": drink_start.text, "drink_end": drink_end.text,
		"best_start": best_start.text, "best_end": best_end.text
		}; 
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

public function addNewBottle(chUpdate:Boolean):void{
	var params:Object = new Object();
	wineService.removeEventListener(ResultEvent.RESULT,resultHandler);
	wineService.removeEventListener(ResultEvent.RESULT,loadWineListHandler);
	wineService.removeEventListener(ResultEvent.RESULT,updateItemHandler);
	wineService.removeEventListener(ResultEvent.RESULT,addAnotherItemHandler)
	wineService.addEventListener(ResultEvent.RESULT,insertItemHandler);
	
	params = {"method": "addNewBottle", "wineID": parseInt(arrWineDetail[0]), "wine_name": inp_wnm.text,
		"region": inp_reg.text, "grape_type": inp_gt.text,
		"appelation": inp_app.text, "classification": inp_cls.text,
		"color": inp_col.text, "year": inp_yr.text,
		"grading": inp_grd.text, "comments": inp_cmt.text,
		"bottleID" : NaN, 
		"price": inp_prc.text, "bottle_size": inp_btsz.text,
		"purchase_date": inp_pd.text, "removal_date": NaN,
		"event_comment": inp_ecmnt.text,
		"drink_start": inp_ds.text, "drink_end": inp_de.text,
		"best_start": inp_bs.text, "best_end": inp_be.text,
		"chUpdate":chUpdate
	};  
	wineService.cancel();
	wineService.send(params);		
}