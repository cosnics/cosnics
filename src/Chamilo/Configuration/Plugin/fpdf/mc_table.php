<?php
require('fpdf.php');

class PDF_MC_Table extends FPDF
{
	var $columnType;
	var $heading;
	var $FontSize = 0;
	/**
	 *  @brief array(logoFilename, middleText, rightRext, style)
	 */
	var $header;
	/**
	 *  @brief array(text, style)
	 */
	var $footer;
	
	function SetColumnType($type)
	{
		$this->columnType=$type;
	}

	function SetHeading($headingData)
	{
		$this->heading = $headingData;
	}
	
	/**
	 *  Header structure: 
	 *  logo              middle text        right text
	 */
	function SetHeader($logoFilename, $middleText, $rightText, $style)
	{
		$this->header = array($logoFilename, $middleText, $rightText, $style);
	}

	/**
	 *  Footer: centered text.
     *
     *  @var $text The string 'PAGENUMBER' will be replaced by current page number.         
	 */
	function SetFooter($text, $style)
	{
		$this->footer = array($text, $style);
	}

	function Row($data)
	{
		//Calculate the height of the row
		$nb=0;
        $max_image_height = 0;
		for($i=0;$i<count($data);$i++)
		{
            $this->Font($data, $i);

            if ($this->IsImage($data[$i]))
            {
                $max_image_height = max($max_image_height, $this->GetImageHeight($this->GetWidth($i), $data[$i]));
            } 
            else
            {
                $nb=max($nb,$this->NbLines($this->GetWidth($i),$data[$i]));
            }
		}

		if ($nb <= 1)
		{
			$cellHeight = $this->FontSize + 3;
		}
		else
		{
            $cellHeight = $this->FontSize + 1;
        }
        
		$h=$cellHeight*$nb;

        $image_bottom_margin = 3;
        $h = max($h, $max_image_height + $image_bottom_margin);

		//Issue a page break first if needed
		$this->CheckPageBreak($h, $data);
		//Draw the cells of the row
		for($i=0;$i<count($data);$i++)
		{
			$this->TextColor($data, $i);
			$this->FillColor($data, $i);
			$this->DrawColor($data, $i);
			$this->Font($data, $i);

			$w=$this->GetWidth($i);
            $a=$this->GetAlignment($i, $data);

			//Save the current position
			$x=$this->GetX();
			$y=$this->GetY();

            // Draw background of cell, because Multicell leaves the background of empty lines white.
            $this->Rect($x,$y,$w,$h, 'F');
		
			//Print the image or text
            if ($this->IsImage($data[$i]))
            {
                $this->Image($data[$i], $x, $y, $w, $h - $image_bottom_margin);   
            }
            else
            {
                $this->MultiCell($w,$cellHeight,$data[$i],0,$a,0);
            }
           
			//Draw the border
			$this->Rect($x,$y,$w,$h, '');
			//Put the position to the right of the cell
			$this->SetXY($x+$w,$y);
		}
		//Go to the next line
		$this->Ln($h);
	}

    function Heading()
	{
		if ($this->heading)
		{
			$this->Row($this->heading);	
		}
	}

    function CheckPageBreak($h, $data)
	{
		//If the height h would cause an overflow, add a new page immediately
		if($this->GetY()+$h>$this->PageBreakTrigger)
		{
			$this->AddPage($this->CurOrientation);
			if ($data !== $this->heading)
			{
				$this->Heading();
			}
		}
	}

	function NbLines($w,$txt)
	{
		//Computes the number of lines a MultiCell of width w will take
		$cw=&$this->CurrentFont['cw'];
		if($w==0)
			$w=$this->w-$this->rMargin-$this->x;
		$wmax=($w-2*$this->cMargin)*1000/($this->FontSize);
		$s=str_replace("\r",'',$txt);
		$nb=strlen($s);
		if($nb>0 and $s[$nb-1]=="\n")
			$nb--;
		$sep=-1;
		$i=0;
		$j=0;
		$l=0;
		$nl=1;
		while($i<$nb)
		{
			$c=$s[$i];
			if($c=="\n")
			{
				$i++;
				$sep=-1;
				$j=$i;
				$l=0;
				$nl++;
				continue;
			}
			if($c==' ')
				$sep=$i;
			$l+=$cw[$c];
			if($l>$wmax)
			{
				if($sep==-1)
				{
					if($i==$j)
						$i++;
				}
				else
					$i=$sep+1;
				$sep=-1;
				$j=$i;
				$l=0;
				$nl++;
			}
			else
				$i++;
		}

		return $nl;
	}

	private function TextColor($data, $columnIndex)
	{
        $color = array(0, 0, 0);

        if ($this->columnType[$columnIndex])
        {
            if ($data === $this->heading)
            {
                $color = $this->columnType[$columnIndex]->getHeadingCellStyle()->getTextColor();
            }
            else
            {
                $color = $this->columnType[$columnIndex]->getDataCellStyle()->getTextColor();
            }
		}

		$this->SetTextColor($color[0], $color[1], $color[2]);
	}

	private function FillColor($data, $columnIndex)
	{
        $color = array(255, 255, 255);

        if ($this->columnType[$columnIndex])
        {
            if ($data === $this->heading)
            {
                $color = $this->columnType[$columnIndex]->getHeadingCellStyle()->getBackgroundColor();
            }
            else
            {
                $color = $this->columnType[$columnIndex]->getDataCellStyle()->getBackgroundColor();
            }
        }

		$this->SetFillColor($color[0], $color[1], $color[2]);
	}

	private function DrawColor($data, $columnIndex)
	{
        $color = array(0, 0, 0);

        if ($this->columnType[$columnIndex])
        {
            if ($data === $this->heading)
            {
                $color = $this->columnType[$columnIndex]->getHeadingCellStyle()->getBorderColor();
            }
            else
            {
                $color = $this->columnType[$columnIndex]->getDataCellStyle()->getBorderColor();
            }
        }

		$this->SetDrawColor($color[0], $color[1], $color[2]);
	}

	private function Font($data, $columnIndex)
	{
        $font = array('Arial', '', 10);
        
		if ($this->columnType[$columnIndex])
        {
            if ($data === $this->heading)
            {
                $font = $this->columnType[$columnIndex]->getHeadingCellStyle()->getFont();
            }
            else
            {
                $font = $this->columnType[$columnIndex]->getDataCellStyle()->getFont();
            }
        }

		$this->SetFont($font[0], $font[1], $font[2]);
	}

	function Header()
	{
		if (! $this->header)
		{
			return;
		}

        $font = $this->header[3]->getHeaderFont();
        $this->SetFont($font[0], $font[1], $font[2]);
        
        $textHeight = $this->FontSize * 1.5;
		
		if ($this->header[0])
		{
			$this->Image($this->header[0], $this->GetX(), $this->GetY(), 0, $textHeight);
		}
		$this->SetX($this->GetAbsoluteWidth(1. / 3.));

        $titleTextColor = $this->header[3]->getHeaderTextColor();
        $this->SetTextColor($titleTextColor[0], $titleTextColor[1], $titleTextColor[2]);
        $this->Cell($this->GetAbsoluteWidth(1. / 3.), $textHeight, $this->header[1], 0, 0, 'C');
		$this->Cell($this->GetAbsoluteWidth(1. / 3.), $textHeight, $this->header[2], 0, 0, 'R');
		$this->Ln($textHeight);
        
        $titleLineColor = $this->header[3]->getHeaderLineColor();
        $this->SetDrawColor($titleLineColor[0], $titleLineColor[1], $titleLineColor[2]);
        $this->Line($this->GetX(), $this->GetY(), $this->GetX() + $this->GetAbsoluteWidth(1.), $this->GetY());
		$this->Ln($textHeight);
	}
	
	function Footer()
	{
        $font = $this->footer[1]->getFooterFont();
        $this->SetFont($font[0], $font[1], $font[2]);

        $textColor = $this->footer[1]->getFooterTextColor();
        $this->SetTextColor($textColor[0], $textColor[1], $textColor[2]);
        
        $textHeight = $this->FontSize * 1.5;
		
        $this->SetY(-$this->bMargin + 2);
		$this->Cell(0, $textHeight, str_replace('PAGENUMBER', $this->PageNo(),  $this->footer[0]), 0, 0, 'C');
	}
	
	function GetWidth($columnIndex)
	{
        $relative_width = 0.1;

        if ($this->columnType[$columnIndex])
        {
            $relative_width = $this->columnType[$columnIndex]->getRelativeWidth();
        }

		return $this->GetAbsoluteWidth($relative_width);
	}

	function GetAbsoluteWidth($relativeWidth)
	{
		if ($this->CurOrientation == 'P')
		{
			$pageWidth = $this->CurPageSize[0];
		}
		else
		{
			$pageWidth = $this->CurPageSize[1];
		}

		$pageWidth -= ($this->lMargin + $this->rMargin);

		return $pageWidth * $relativeWidth;
	}

	function GetAbsoluteHeight($relativeHeight)
	{
		if ($this->CurOrientation == 'P')
		{
			$pageHeight = $this->CurPageSize[1];
		}
		else
		{
			$pageHeight = $this->CurPageSize[0];
		}

		$pageHeight -= ($this->tMargin + $this->bMargin);

		return $pageHeight * $relativeHeight;
	}	

    private function GetAlignment($columnIndex, $data)
	{
        $alignment = 'L'; 
        
        if ($this->columnType[$columnIndex])
        {
            if ($data === $this->heading)
            {
                $alignment = $this->columnType[$columnIndex]->getHeadingCellStyle()->getAlignment();
            }
            else
            {
                $alignment = $this->columnType[$columnIndex]->getDataCellStyle()->getAlignment();
            }
        }

        return $alignment;
    }

	private function IsImage($data)
	{
        $extensions = array('.png', '.jpg', '.gif');

        foreach ($extensions as $extension)
        {
            if (substr_compare(strtolower($data), $extension, -strlen($extension)) === 0)
            {
                return true;
            }
        }

        return false;
    }

    private function GetImageHeight($columnWidth, $filename)
	{
        $size = getimagesize($filename);
        $widthInPixels = $size[0];
        $heightInPixels = $size[1];

        return (float)$heightInPixels / (float)$widthInPixels * $columnWidth;
    }
}
?>
