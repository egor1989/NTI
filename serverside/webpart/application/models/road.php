<?php
class Road extends CI_Model {
  function __construct()
    {
        parent::__construct();
    }
    
	function create_road()
    {

	$this->db->insert('element'); 
	$id=$this->db->insert_id();
	$data = array('Id' => $id);
	$query=$this->db->insert('Road', $data); 
	return $id;
	
	}
	function create_road_leg()
    {
	$lat1=trim($this->input->post('lat1'));
	$lng1=trim($this->input->post('lng1'));
	$lat2=trim($this->input->post('lat2'));
	$lng2=trim($this->input->post('lng2'));
	$id=trim($this->input->post('id'));
	$data = array('Lat1' => $lat1 ,'Lng1' => $lng1,'Lat2' => $lat2 ,'Lng2' => $lng2,'Road_Id'=>$id);
	$query=$this->db->insert('Road_leg', $data); 
	$insert_id=$this->db->insert_id();
	
	return $insert_id;
	
	
	}
		function get_to_decode()
    {
		$this->db->limit("300");
		$this->db->order_by("RAND()");
		$query = $this->db->get_where('Hole_Area', array('Status' => 0));
		return $query->result();
	}
	function get_road_info()
	{
		$id=trim($this->input->post('id'));
		$query = $this->db->get_where('Road_leg_status', array('Roadleg_id' => $id));
		return $query->result();
	}
	
	function create_info()
    {//$.post('/functions/create_road_info', {id:lineid,color:linecolor,weight:lineweight,info:lineinfo}, function(data) 

	$id=trim($this->input->post('id'));
	$color=trim($this->input->post('info'));
	$weight=trim($this->input->post('address'));
	$info=trim($this->input->post('status'));
	$where =array('Roadleg_id' => $id);
	$query = $this->db->get('Road_leg_status', $where);
	if($query->num_rows()>0)
	{
		$data = array('Status' => $info, 'Weight' => $weight, 'Color' => $color);
		$query = $this->db->update('Road_leg_status', $data, $where);
	}
	else
	{
		$data = array('Status' => $info, 'Weight' => $weight, 'Color' => $color,'Roadleg_id'=>$id);
		$query = $this->db->insert('road_leg_status', $data);
	}
    
	
	return $query->result();
	}
	
	
	function gradientup()
    {


		$this->db->truncate('Gradientlink'); 
	
		$query = $this->db->query('SELECT distinct(Address) from Hole_Area Where LENGTH(Address)>5 and Gradientlink=0');
		$j=0;
		foreach ($query->result() as $row)
		{
			$Holes[$j]=$row;//its id
			$j++;
			
		}
		$k=0;
		$Gradient[$k]="";
		for($j=0;$j<count($Holes);$j++)
		{
			
			$founded=0;
			for($i=0;$i<count($Gradient);$i++)
			{
				$ps=0;
				similar_text($Holes[$j]->Address,$Gradient[$i],$ps);
				if($ps>95)$founded=1;
		
				
			}
			if($founded!=1)
			{
			$Gradient[$k]=$Holes[$j]->Address;
				$data = array('Street' => $Holes[$j]->Address);
				$this->db->insert('Gradientlink', $data); 
			$k++;
			}
			
		}
		
		
		
		$query = $this->db->query('SELECT * from Gradientlink');
		$i=0;
		unset($Gradient);
		foreach ($query->result() as $row)
		{
			$Gradient[$i]=$row;//its id
			echo $Gradient[$i]->Id."   ".$Gradient[$i]->Street."<br/>";
			$i++;
		}
		
		
		/*

*/

	}
	function gradient_hole_update()
	{
		
			$query = $this->db->query('SELECT * from Hole_Area Where LENGTH(Address)>5 and Gradientlink=0 Limit 5000');
		$j=0;
		foreach ($query->result() as $row)
		{
			$Holes[$j]=$row;//its id
			$j++;
			
		}
		
	
		$query = $this->db->query('SELECT * from Gradientlink');
		$i=0;
		unset($Gradient);
		foreach ($query->result() as $row)
		{
			$Gradient[$i]=$row;//its id
			echo $Gradient[$i]->Id."   ".$Gradient[$i]->Street."<br/>";
			$i++;
		}
		
		
			for($j=0;$j<count($Holes);$j++)
	{
			$maxps=0;
			$fi=0;
			for($i=0;$i<count($Gradient);$i++)
			{
				
				similar_text($Holes[$j]->Address,$Gradient[$i]->Street,$ps);
				
				if($ps>$maxps)
					{
						$maxps=$ps;
						$fi=$Gradient[$i]->Id;
						$strrr=$Gradient[$i]->Street;
					}
			}
			
			$data = array('Gradientlink' => $fi);
			$this->db->where('Id', $Holes[$j]->Id);
			$this->db->update('Hole_Area', $data); 	
	
	}
		
		
	}
	
	function gradient_load()
    {

		$query = $this->db->query('SELECT distinct(Gradientlink) from Hole_Area');
		$i=0;
		foreach ($query->result() as $row)
		{
			$rid[$i]=$row->Gradientlink;//its id	
			$i++;
		}

		for($j=0;$j<$i;$j++)
		{
			$this->db->where('Gradientlink', $rid[$j]);
			$this->db->order_by('LLat DESC,LLng DESC'); 
			$this->db->limit(1);
			$query = $this->db->get('Hole_Area');
			foreach ($query->result() as $row)
				{
					$dump[$j]['Link']=$row->Gradientlink;
					$dump[$j]['Lat1']=$row->LLat;
					$dump[$j]['Lng1']=$row->LLng;
				}
			$this->db->where('Gradientlink', $rid[$j]);
			$this->db->order_by('LLat ASC,LLng ASC'); 
			$this->db->limit(1);
			$query = $this->db->get('Hole_Area');
			foreach ($query->result() as $row)
			{
				$dump[$j]['Lat2']=$row->LLat;
				$dump[$j]['Lng2']=$row->LLng;
			}
			$this->db->where('Gradientlink', $rid[$j]);
			$this->db->select('Count(*) wg');
			$query = $this->db->get('Hole_Area');
			foreach ($query->result() as $row)
			{
				$dump[$j]['Count']=$row->wg;
				
			}
				
				
				
			$this->db->where('Gradientlink', $rid[$j]);
			$this->db->select('sum(Weight)/Count(*) wg');
				
			$query = $this->db->get('Hole_Area');
			foreach ($query->result() as $row)
				{
						$true_weight=(float)$row->wg;
				}
				
				
				
				$dump[$j]['Weight']=0;
				
				$z=1;
				
						$this->db->where('Gradientlink', $rid[$j]);
				$query = $this->db->get('Hole_Area');
				
				foreach ($query->result() as $row)
				{
					if($row->Weight>$true_weight){$dump[$j]['Weight']+=$row->Weight-$true_weight;$z++;}
				}
		
			$dump[$j]['Weight']=$dump[$j]['Weight']/$z;
					$dump[$j]['Distance']=0;
				
				
				
				
				
				
				
				


		}

	return $dump;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	function gradient_debug()
    {

		$query = $this->db->query('SELECT distinct(Gradientlink) from Hole_Area');
		$i=0;
		foreach ($query->result() as $row)
		{
			if($row->Gradientlink!=0)
			{$rid[$i]=$row->Gradientlink;//its id	
			$i++;
			}
		}
		// перебираем ID градиентов 
		$r_i=0;
		for($j=0;$j<$i;$j++)
		{
		//	$this->db->where('Gradientlink', $rid[$j]);
		$glink=$rid[$j];
		//	$this->db->order_by('LLat DESC,LLng DESC'); 
		//	$query = $this->db->get('Hole_Area');
		
			$query = $this->db->query("Select Gradientlink,LLat,LLng,Weight,Date from Hole_Area where Gradientlink=$glink order by LLat DESC,LLng DESC");
			$z=0;
			//Собрали все точки 
			 $data=$query->result_array();
			foreach ($data as $row)
				{
					$dump[$j][$z]['Lat']=$row['LLat'];
					$dump[$j][$z]['Lng']=$row['LLng'];
					$dump[$j][$z]['Weight']=$row['Weight'];
					$dump[$j][$z]['Link']=$row['Gradientlink'];
					$dump[$j][$z]['Date']=$row['Date'];
					
					$z++;
					
				}
		}
		$k=0;
for($j=0;$j<$i;$j++)
{
		$true_length=0;
			$true_weight=0;
			$true_count=1;
			$start_lat=$dump[$j][0]['Lat'];
			$start_lng=$dump[$j][0]['Lng'];
			unset($temp_weight);
			$w=0;
	for($z=0;$z<count($dump[$j])-1;$z++)
	{
		$minlat=$dump[$j][$z]['Lat'];
		$minlng=$dump[$j][$z]['Lng'];
		$maxlat=$dump[$j][$z+1]['Lat'];
		$maxlng=$dump[$j][$z+1]['Lng'];
		$length=sqrt(($maxlat-$minlat)*($maxlat-$minlat)+($maxlng-$minlng)*($maxlng-$minlng));
		if(($true_length+$length)>0.002)
		{
			if($true_length>0)
			{
			
				$ret_data[$k]['Lat1']=$start_lat;
				$ret_data[$k]['Lat2']=$maxlat;
				$ret_data[$k]['Lng1']=$start_lng;
				$ret_data[$k]['Lng2']=$maxlng;
				$ret_data[$k]['Weight']=$true_weight/$true_count;
				$ret_data[$k]['Link']=$dump[$j][$z]['Link'];
				$true_weight=0;
				$tmp_weight_count=1;
				
				
				for($w=0;$w<count($temp_weight);$w++)
				{
					for($v=0;$v<count($temp_weight)-$w-1;$v++)
					{
						if($temp_weight[$v]['Date']>$temp_weight[$v+1]['Date'])
						{
							$tmp=$temp_weight[$v];
							$temp_weight[$v]=$temp_weight[$v+1];
							$temp_weight[$v+1]=$tmp;
						}
					}
					//if($temp_weight[$w]['Weight']<0)$temp_weight[$w]['Weight']=$temp_weight[$w]['Weight']*(-1);
					//if($temp_weight[$w]['Weight']>($ret_data[$k]['Weight'])){$true_weight+=($temp_weight[$w]['Weight']-$ret_data[$k]['Weight']);$tmp_weight_count++;}
					//echo $temp_weight[$w]['Date']."    ".$temp_weight[$w]['Weight']."<br/>";
				
				}
			
				/*
				for($w=0;$w<count($temp_weight);$w++)
				{
					echo $temp_weight[$w]['Date']."    ".$temp_weight[$w]['Weight']."<br/>";
				}
				*/
					$roadway_count=1;
					$temp_wg=0;
					$temp_count=1;
				for($w=0;$w<count($temp_weight)-1;$w++)
				{
					if($temp_weight[$w+1]['Date']-$temp_weight[$w]['Date']<600)
					{
						//$grouped_point[$grouped_count][$grouped_entry]=$temp_weight[$w+1]['Date'];
					$temp_wg+=$temp_weight[$w+1]['Weight'];
					
						$temp_count=$temp_count+1;
						
					}
					else
					{
					
						$temp_wg=$temp_wg/$temp_count;
						$true_weight+=$temp_wg;
					
						$temp_count=1;
						$roadway_count++;
					}
				}
				$temp_wg+=$temp_weight[$w]['Weight'];
				$temp_count=$temp_count+1;
				$temp_wg=$temp_wg/$temp_count;
				$true_weight+=$temp_wg;
				$true_weight=$true_weight/$roadway_count;
		
			//	if($true_weight>30)echo $true_weight."<br/>";
		
				
				$ret_data[$k]['Weight']+=($true_weight/$tmp_weight_count);
				
				
				$k++;
				
			}
			$true_length=0;
			$true_weight=0;
			unset($temp_weight);
			$w=0;
			$start_lat=$dump[$j][$z+1]['Lat'];
			$start_lng=$dump[$j][$z+1]['Lng'];
			$true_count=1;
		}
		else
		{
			$true_count++;
			$true_length+=$length;
			$temp_weight[$w]['Weight']=$dump[$j][$z]['Weight'];
			$temp_weight[$w]['Date']=$dump[$j][$z]['Date'];
			
			if($dump[$j][$z]['Weight']>=0)$true_weight+=$dump[$j][$z]['Weight'];
			else
			$true_weight-=$dump[$j][$z]['Weight'];
			$w++;
			
		}
		
	}
}
for($i=0;$i<$k;$i++)
{
	$data = array('Lat1' => $ret_data[$i]['Lat1'],'Lng1' => $ret_data[$i]['Lng1'],'Lat2' => $ret_data[$i]['Lat2'],'Lng2' => $ret_data[$i]['Lng2'],'Weight' => $ret_data[$i]['Weight'],'Link' => $ret_data[$i]['Link']);

	$this->db->insert('Gradient', $data); 
}
return $ret_data;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function gradient()
    {

		$query = $this->db->query('SELECT * from Gradient');
		$i=0;
		foreach ($query->result() as $row)
		{
			$rid[$i]['Link']=$row->Link;//its id
			$rid[$i]['Weight']=$row->Weight;//its id
			$rid[$i]['Lat1']=$row->Lat1;//its id
			$rid[$i]['Lng1']=$row->Lng1;//its id
			$rid[$i]['Lat2']=$row->Lat2;//its id
			$rid[$i]['Lng2']=$row->Lng2;//its id
			$i++;
		}

	return $rid;
	}
	
	
	
	function load_kad()
    {

			$j=0;
			$query = $this->db->get('Gradientup');
			foreach ($query->result() as $row)
				{
					$dump[$j]['Weight']=1;
					$dump[$j]['Lat']=$row->Lat;
					$dump[$j]['Lng']=$row->Lng;
					$j++;
				}
	return $dump;
	}
	
	
	function delete()
	{
		$id=trim($this->input->post('id'));
		$data = array('Deleted' => 1);
		$where =array('Nr' => $id);
		$query = $this->db->update('Road_leg', $data, $where);
		return $query->result();
	}

}

