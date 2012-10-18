<?
require('_common.php');

if (isUserAtLeastAdmin()) 
{

include('_reporters.php');
require('FPDF/fpdf.php');

class PDF extends FPDF
{
    function __construct($orient, $unit, $size, $obj) // hack to get round single inheritance
    {
       parent::__construct($orient, $unit, $size);
       $this->obj = $obj;
    }

     function AcceptPageBreak()
    {
        $this->SetXY(10,10);
        $this->obj->x = $this->GetX();
        $this->obj->xo = $this->obj->x;
        $this->obj->y = $this->GetY();
        return true;
    }
}

// This is one greedy script
set_time_limit ( 60 );
ini_set('memory_limit', '128M');

define("SYMS_PER_LINE", 16);
define("CELL_WIDTH", 10);
define("CELL_HEIGHT", CELL_WIDTH);
define("CELL_MARGIN", 1);
define("TEXT_HEIGHT", 2);
define("CAT_FONT_SIZE", 15);
define("LABEL_FONT_SIZE", 5);
define("ROW_SPACING", 2);
define("BOTTOM_MARGIN", CELL_HEIGHT + 2 * CELL_MARGIN + 3 * TEXT_HEIGHT);

if ($_SERVER['QUERY_STRING'] == 's=live')
  $QOPT = '= 4';
else
  $QOPT = 'IN (2,3,4)';

$SQL = <<<EOT
    SELECT
    mc.name AS category
    , REPLACE(m.name, '_', ' ') AS name
    , CONCAT('media/symbols/EN/preview/m-', m.name, '.gif') AS symbol
    , m.status_id
    FROM t_media m
    LEFT JOIN t_media_category AS mc
     ON m.category_id = mc.id
    WHERE m.status_id $QOPT AND (mc.name like 'A%' OR 1)
    ORDER BY mc.name, m.name;
EOT;

$lastCat = '';
$cachedRow = NULL;
function getRow($result) // file scope as not possibel to call function variable to method
{
    global $lastCat, $cachedRow;
    $row = Array();
    for ($i = 0; $i < SYMS_PER_LINE; $i++)
    {
        if ($cachedRow !== null)
        {
  //          print_r($cachedRow);
            $dbrow = $cachedRow;
            $cachedRow = null;
        }
        else
        {
            $dbrow = mysql_fetch_assoc($result);
            if (!$dbrow && $i == 0)
                return False; // no more symbols at all so trigger exit
            elseif (!$dbrow)
                break; // no more symbols for this row
        }
    
        if ($dbrow['category'] != $lastCat)
        {
            if ($lastCat != '')
            {
//                print('zzz '.$dbrow['category'].' @'.$lastCat);
                $lastCat = $dbrow['category'];
                $cachedRow = $dbrow;
                break;
            }
            else
            {
                $lastCat = $dbrow['category'];
            }
        }
        $row[] = $dbrow;
    }
    return $row;
}

class PDFSymbolList extends ReporterABC
{
    function __construct()
    {
    	global $SQL;
        parent::__construct($SQL, null, null, 'getRow');
        $this->category = null;
    }
    
    protected function openOutput()
    { 
        $pdf=new PDF('P', 'mm', 'A4', $this);
        $pdf->SetAutoPageBreak(true, BOTTOM_MARGIN );

 //       $pdf->SetFont('Arial','',CAT_FONT_SIZE); // for Ln()
        $pdf->SetTitle('Mulberry symbol set');
        $pdf->SetSubject('All symbols organised by category');
        $pdf->SetCreator('Somehting.com');
        $pdf->SetKeyWords('symbols accessibility aac communication');

        $pdf->AddPage();
        
        $this->pdf = $pdf;
    }

    protected function closeOutput()
    {
        $this->pdf->Output(); // open in browser
    }

// cant find how to ref this in constructor so using global function - e.g no poss to pass bound func ref
/*    protected function getRow($result)
    {
    }
*/    
    protected function putHeader($row)
    {
    }
    
    protected function putLine($row)
    {
        $pdf = $this->pdf;
        
        if ($row[0]['category'] <> $this->category)
        {
            if ($this->category)
            {
                $pdf->SetXY($pdf->GetX(), $pdf->GetY() + 3 * ROW_SPACING);
            }
            $this->category = $row[0]['category'];
            
            $pdf->SetFont('Arial','',CAT_FONT_SIZE);
            $pdf->Cell(0, 0, $this->category, 0, 2, 'L');
            $pdf->SetXY($pdf->GetX(), $pdf->GetY() + 2 * ROW_SPACING);
        }
        {
            $pdf->SetFont('Arial','',LABEL_FONT_SIZE);

            $this->x = $pdf->GetX();
            $this->xo = $this->x;
            $this->y = $pdf->GetY();
            foreach($row as $dbrow)
            {
                $pdf->SetXY($this->x, $this->y);
                $pdf->Image($dbrow['symbol'], null, null, CELL_WIDTH, CELL_HEIGHT);
                $pdf->SetXY($this->x, $this->y + CELL_HEIGHT + CELL_MARGIN);
                if ($dbrow['status_id'] == 2) // uplaoded
                	$pdf->SetDrawColor(230, 0, 0);
                elseif ($dbrow['status_id'] == 3) // review
                	$pdf->SetDrawColor(207,203,70);
                else
                	$pdf->SetDrawColor(255,255,255);
                $pdf->MultiCell(CELL_WIDTH, TEXT_HEIGHT, $dbrow['name'], 'TBLR', 'C', false);
                $this->x += CELL_WIDTH + CELL_MARGIN;
            }
            $pdf->SetXY($this->xo, $pdf->GetY() + CELL_MARGIN + ROW_SPACING);
        }
    }

    protected function putFooter()
    {
    }
    
}

$reporter = new PDFSymbolList();
$reporter->run();


} // endif
?>