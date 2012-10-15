<?

abstract class ReporterABC
{
    function __construct($sql, $wide=False, $getHdr=null, $getRow=null)
    {
       $this->sql = $sql;
       $this->wide = $wide;
       $this->getHeader = $getHdr;
       $this->getRow = $getRow;
    }

    function __destruct() 
    {
    }            
    
    abstract protected function openOutput();
    abstract protected function closeOutput();
    abstract protected function putHeader($row);
    abstract protected function putLine($row);
    abstract protected function putFooter();
    
    public function run()
    {
        db_connect();
        $result = db_runQuery($this->sql);
        if (!$result)
        {
           die("query failed" );
        }

        $this->openOutput();
        
        if ($this->getHeader)
        {
        	$f = $this->getHeader;
        	$row = $f($result);
        }
        else
        {
	        $row = mysql_fetch_assoc($result);
	        $row = array_keys($row);
        }
        $this->putHeader($row);

        mysql_data_seek($result, 0);
        $fRow;
        if ($this->getRow)
        {
        	$fRow = $this->getRow; // string of name - oh give me Python
        }
        else
        {
        	$fRow = 'mysql_fetch_row';
        }

        while (!is_bool($row = $fRow($result) ))
        {
        	if (count($row)) // bit fragile
            	$this->putLine($row);
        }

        $this->putFooter();

        $this->closeOutput();
        
        db_disconnect();
    }
}

class CSVReporter extends ReporterABC
{
    protected $handle;
    protected $filename;

    protected function openOutput()
    {
        global $loggedUser;
        $filename = 'tmp/report_'.$loggedUser.'.csv';
        if  (($handle = @fopen($filename, 'wb')) == 0)
        {
            echo "error opening report file";
            exit;
        }
        $this->handle = $handle;
        $this->filename = $filename;
    }

    protected function closeOutput()
    {
        fclose($this->handle);
        header("Location: " . $this->filename);
        exit();
    }
    
    protected function write_csv_line($handle, $arLine)
    {
        $arLine = str_replace('"', '""', $arLine);
        $line = "\"" . join('","', $arLine) . "\"\r\n";
        print($line).'<br>';
        return fwrite($handle, $line);
    }

    protected function putHeader($row)
    {
        @$this->write_csv_line($this->handle, $row);
    }
    
    protected function putLine($row)
    {
        @$this->write_csv_line($this->handle, $row);
    }
    
    protected function putFooter()
    {
    }
}

class HTMLReporter extends ReporterABC
{

    protected function openOutput()
    {
    }

    protected function closeOutput()
    {
    }
    
    protected function write_HTML_line($arLine, $pre, $post)
    {
        $line = $pre . join($post.$pre, $arLine) . $post;
        print($line);
    }

    protected function putHeader($row)
    {
    	$klass = 'adreport' . (($this->wide) ? ' wide' : '');
        print('<table class="'.$klass.'" ><thead><tr>');
        @$this->write_HTML_line($row, '<th>', '</th>');
        print('</tr></thead>');
    }
    
    protected function putLine($row)
    {
        print('<body><tr>');
        @$this->write_HTML_line($row, '<td>', '</td>');
        print('</tbody><tr>');
    }
    
    protected function putFooter()
    {
        print('</table>');
    }
}
?>