
<!-- <script src="https://cdn.amcharts.com/lib/4/core.js"></script>
<script src="https://cdn.amcharts.com/lib/4/charts.js"></script>
<script src="https://cdn.amcharts.com/lib/4/themes/animated.js"></script>
<script src="//www.amcharts.com/lib/4/lang/pt_BR.js"></script> -->
<style>
<?php echo ".chartdiv".$this->id; ?> {
  width: 100%;
  height: <?php echo $this->height; ?>;
}
.amcharts-amexport-icon{
  cursor: pointer;
}
.amcharts-amexport-item-level-0 > .amcharts-amexport-icon{
  padding: 0px;
}
<?php echo ".legenddiv".$this->id; ?>{
  height: 35px;
}
.btnexport{
  position: absolute;
  top: 100px;
  right: 35px;
  <?php if ($pagina === "compare" && !$unico) { echo "top: 340px;"; echo "right: 20px;";  }
  else if ($unico $$ $pagina === "compare"){ echo "top: 335px;"; }
  else if ($unico){ echo "top: 30px;"; }
  ?>  
  padding: 1em 2em;
  opacity: 1  !important;
  width: 30px;
  min-height: 30px;
  border-radius: 3px;
  padding: 0px;
  margin: 1px 1px 0px 0px;
  color: rgb(0, 0, 0);
  transition: all 100ms ease-in-out 0s, opacity 0.5s ease 0.5s;
  background-color: rgb(217, 217, 217);
  border: 0px;
  padding-bottom: 0px;
  cursor: pointer;
}
.btnexport:active{
  padding-bottom: 0;
  opacity: 1;
}
.btnexport:hover{
  background: rgb(163, 163, 163) none repeat scroll 0% 0%;
  color: rgb(0, 0, 0);
  opacity: 0.9;
  padding-bottom: 0px;
}
</style>
<!-- Chart code -->
<script>
am4core.ready(function() {

// Themes begin
am4core.useTheme(am4themes_animated);
// Themes end

// Create chart instance
var chart = am4core.create("chartdiv<?php echo $this->id; ?>", am4charts.XYChart);

chart.responsive.enabled = true;
am4core.options.autoSetClassName = true;

let legendContainer = am4core.create("legenddiv<?php echo $this->id; ?>", am4core.Container);
legendContainer.width = am4core.percent(100);
legendContainer.height = am4core.percent(100);

chart.colors.list = [
//Cores do obejto gráfico de linha
<?php
for($i=0;$i<sizeof($this->series_colors);$i++){
  if (strpos($this->series_colors[$i], "#") === 0){
    if ($i !== sizeof($this->series_colors)-1){
      echo "am4core.color('".$this->series_colors[$i]."'),";
    }else{
      echo "am4core.color('".$this->series_colors[$i]."')";
    }
  }
}
?>
];
<?php
// $isnulldata = false;
echo "chart.data = [";
for($i=0;$i<sizeof($this->years);$i++){
  // if ($i === 0){
  //   echo "{'date':'".$this->years[$i]."'},";
  // }else{
    echo "{'date':'".$this->years[$i]."',";
    for($j=0;$j<sizeof($this->series);$j++){
      if ($j !== sizeof($this->series)-1){
        if ( $this->data[$i][$this->years[$i]."_".$this->series[$j]]){
          echo "'".$this->series[$j]."':'".$this->data[$i][$this->years[$i]."_".$this->series[$j]]."',";
        }else{
          echo "'dummy':'',";
        }
      }else{
        if ($this->data[$i][$this->years[$i]."_".$this->series[$j]]){
          echo "'".$this->series[$j]."':'".$this->data[$i][$this->years[$i]."_".$this->series[$j]]."'";
        }else{
           echo "'dummy':''";
        }
      }
    }
    if ($i ===  sizeof($this->years)-1){
      echo "}";
    }else{
      echo "},";
    } 
  // }
}
echo "];";
?>
//adiciona formato especial a tooltip
chart.numberFormatter.numberFormat = "<?php echo utf8_encode($this->tooltip_before)." "; ?> #.# <?php echo "  ".utf8_encode($this->tooltip_after); ?>";

// Create axes
var dateAxis = chart.xAxes.push(new am4charts.DateAxis());
dateAxis.renderer.grid.template.location = 0;
dateAxis.renderer.minGridDistance = 10;

<?php
if ($this->stacked === "1"){
  echo "dateAxis.renderer.minGridDistance = 10;\n";
  echo "dateAxis.startLocation = 0.5;\n";
  echo "dateAxis.endLocation = 0.5;\n";
  echo "dateAxis.baseInterval = {timeUnit: 'year',count: 1}";
}?>

var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
valueAxis.logarithmic = false;
valueAxis.renderer.minGridDistance = 20; //padrão do amcharts 20, com 15 exibe os decimais
<?php if ($this->inf_lim || $this->inf_lim  === "0") { ?>
valueAxis.min = <?php echo $this->inf_lim; ?>;
<?php  } ?>
<?php if ($this->up_lim) { ?>
valueAxis.max = <?php echo $this->up_lim; ?>;
<?php  } ?>

// Axis tooltip Category
let axisTooltip = dateAxis.tooltip;
axisTooltip.background.fill = am4core.color("#575756");
axisTooltip.background.strokeWidth = 0;

// Axis tooltip Value
let axisTooltip2 = valueAxis.tooltip;
axisTooltip2.background.fill = am4core.color("#575756");
axisTooltip2.background.strokeWidth = 0;
axisTooltip2.dx = 5;


<?php 
// Create each series

  for($j=0;$j<sizeof($this->series);$j++){

    echo "var series".$j." = chart.series.push(new am4charts.LineSeries());\n";
    echo "series".$j.".dataFields.valueY = '".$this->series[$j]."';\n";
    echo "series".$j.".dataFields.dateX = 'date';\n";
    if ($this->tooltip_before && $this->tooltip_after){
      echo "valueAxis.numberFormatter.numberFormat = \"'".utf8_encode($this->tooltip_before)."'  #.#  '"
      .utf8_encode($this->tooltip_after)."'\";\n";
    }else if (!$this->tooltip_before && $this->tooltip_after){
      echo "valueAxis.numberFormatter.numberFormat = \"#.#  '".utf8_encode($this->tooltip_after)."'\";\n";
    }else if ($this->tooltip_before && !$this->tooltip_after){
      echo "valueAxis.numberFormatter.numberFormat = \"'".utf8_encode($this->tooltip_before)."'  #.#  \";\n";
    }

    echo "series".$j.".name = '".utf8_encode($this->series_label[$j])."';\n";
    echo "series".$j.".tensionX = 0.8;\n";
    echo "series".$j.".strokeWidth = 6;\n";
    echo "series".$j.".strokeWidth = 6;\n";
    echo "series".$j.".tooltipText = '{valueY}[/]';\n";
    echo "series".$j.".hiddenInLegend = false;\n";
    echo "series".$j.".connect = false;\n";
    echo "series".$j.".tooltipText = \"".utf8_encode($this->tooltip_before)." {valueY.formatNumber('#.###')}"
    .utf8_encode($this->tooltip_after)."[/]\";\n";

    if ($this->stacked === "1"){
      echo "series".$j.".sequencedInterpolation = true;\n";
      echo "series".$j.".stacked = true;\n";
      echo "series".$j.".fillOpacity = 0.6;\n";
      echo "series".$j.".tensionX = 1;\n";
    }else{
      echo "var bullet = series".$j.".bullets.push(new am4charts.CircleBullet());\n";
      echo "bullet.circle.fill = am4core.color('#fff');\n";
      echo "bullet.circle.strokeWidth = 3;\n";
    }
  } 

?>
<?php if ($this->up_lim === "100"){ ?> // cortar eixo y acima de 100
  valueAxis.renderer.labels.template.adapter.add("text", (label, target, key) => {
  if (target.dataItem && (target.dataItem.value > 100 )) {
    return "";
  } else {
    return label;
  }
 });
<?php } ?>
// Add cursor
chart.cursor = new am4charts.XYCursor();
chart.cursor.fullWidthLineX = true;
chart.cursor.xAxis = dateAxis;
chart.cursor.lineX.strokeWidth = 0;
chart.cursor.lineX.fill = am4core.color("#000");
chart.cursor.lineX.fillOpacity = 0.1;

// legendas    
chart.legend = new am4charts.Legend();
chart.legend.position = "bottom";
chart.legend.valign = "middle";
chart.legend.contentAlign = "center";
chart.legend.parent = legendContainer;

// tranforma legenda padrão de retângulo para círculo
chart.legend.useDefaultMarker = true;
var marker = chart.legend.markers.template.children.getIndex(0);
marker.cornerRadius(24, 24, 24, 24);
marker.strokeWidth = 6;
marker.strokeOpacity = 1;
marker.stroke = am4core.color("#FFFFFF");

<?php if(verify_login()){ ?>

chart.exporting.menu = new am4core.ExportMenu();
chart.exporting.extraSprites.push({
  "sprite": legendContainer,
  "position": "bottom",
  "marginTop": 20
});
chart.exporting.menu.items = [{
  "label": "...",
  "menu": [
    { "type": "png", "label": "PNG", "options": { "quality": 1 } },
    { "type": "json", "label": "JSON", "options": { "indent": 2, "useTimestamps": true } },
    { "label": "JPG", "type": "jpg" },
    { "label": "CSV", "type": "csv" },
    { "type": "xlsx", "label": "XLSX" }
  ]
}];
chart.exporting.menu.items[0].icon = "../assets/images/svg/Download.svg";

chart.exporting.adapter.add("pdfmakeDocument", function(pdf, target) {

// Add title to the beginning
pdf.doc.content.unshift({
  text: "Regional revenue comparison",
  margin: [0, 30],
  style: {
    fontSize: 25,
    bold: true,
  }
});

return pdf;
});

<?php } ?>
   
}); // end am4core.ready()
</script>
<!-- </head>
<body> -->
	<!-- HTML -->
  <div class="chartdiv<?php echo $this->id; ?>"></div>
  <div class="legenddiv<?php echo $this->id; ?>"></div>
  <?php if(!verify_login()){ ?>
  <img class="btnexport" src="../assets/images/svg/Download.svg" onclick="" data-target="#alertaNaoCadastrado" data-toggle="modal">
  <!-- <input type="button" class="btnexport" value="..." onclick="" data-target="#alertaNaoCadastrado" data-toggle="modal" /> -->
  
  <?php } ?>
<!-- </body>
</html> -->