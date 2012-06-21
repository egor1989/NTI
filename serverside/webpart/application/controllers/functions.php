<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class functions extends CI_Controller {
	public function get()
	{	
		if($this->session->userdata('rights')==3)
		{
					$this->load->model('lays_model');
					$this->load->library('session');
					$admin_data= $this->session->userdata('page');
					$rs= $this->lays_model->LoadRawDataWithNormal($admin_data);
					$k=0;
					$prespeed=0;
					$predir=0;
					$arprt[$k]['maxspeed']=0;
					$arprt[$k]['minspeed']=10000;
					$arprt[$k]['duration']=1;
					for($i=0;$i<count($rs)-1;$i++)
					{
						if(($rs[$i+1]['TypeAcc']=="acc1 started" || $rs[$i+1]['TypeAcc']=="acc1 continued") && ($rs[$i]['TypeAcc']=="acc1 started" || $rs[$i]['TypeAcc']=="acc1 continued"))
						{
							$arprt[$k]['duration']+=$rs[$i+1]['utimestamp']-$rs[$i]['utimestamp'];
							$arprt[$k]['type']="Acc";
							$arprt[$k]['weight']=1;
								$arprt[$k]['G']=$rs[$i]['accx'];
							if($arprt[$k]['maxspeed']<$rs[$i]['speed'])$arprt[$k]['maxspeed']=$rs[$i]['speed'];
							if($arprt[$k]['minspeed']>$rs[$i]['speed'])$arprt[$k]['minspeed']=$rs[$i]['speed'];
							$arprt[$k]['lat']=$rs[$i]['lat'];
							$arprt[$k]['lng']=$rs[$i]['lng'];$arprt[$k]['tm']=$rs[$i]['utimestamp'];
						}
						if(($rs[$i+1]['TypeAcc']!="acc1 started" && $rs[$i+1]['TypeAcc']!="acc1 continued") && ($rs[$i]['TypeAcc']=="acc1 started" || $rs[$i]['TypeAcc']=="acc1 continued"))
						{
							
							$arprt[$k]['type']="Acc";
							$arprt[$k]['weight']=1;	$arprt[$k]['G']=$rs[$i]['accx'];

							if($arprt[$k]['maxspeed']<$rs[$i]['speed'])$arprt[$k]['maxspeed']=$rs[$i]['speed'];
							if($arprt[$k]['minspeed']>$rs[$i]['speed'])$arprt[$k]['minspeed']=$rs[$i]['speed'];
							$arprt[$k]['lat']=$rs[$i]['lat'];
							$arprt[$k]['lng']=$rs[$i]['lng'];
							$arprt[$k]['prespeed']=$prespeed;
							$arprt[$k]['predir']=$predir;$arprt[$k]['tm']=$rs[$i]['utimestamp'];
							$arprt[$k]['time']=date('j/m/y;h:i:s',$rs[$i]['utimestamp']);
							$k++;				
							$arprt[$k]['maxspeed']=0;
							$arprt[$k]['minspeed']=1000;
								$arprt[$k]['duration']=1;
						}
						else
						{
							$prespeed=$rs[$i]['speed'];
							$predir=$rs[$i]['direction'];
						}
						
					}
					
					
					for($i=0;$i<count($rs)-1;$i++)
					{
						if(($rs[$i+1]['TypeAcc']=="acc2 started" || $rs[$i+1]['TypeAcc']=="acc2 continued") && ($rs[$i]['TypeAcc']=="acc2 started" || $rs[$i]['TypeAcc']=="acc2 continued"))
						{
							$arprt[$k]['duration']+=$rs[$i+1]['utimestamp']-$rs[$i]['utimestamp'];
							$arprt[$k]['type']="Acc";
							$arprt[$k]['weight']=2;	$arprt[$k]['G']=$rs[$i]['accx'];

							if($arprt[$k]['maxspeed']<$rs[$i]['speed'])$arprt[$k]['maxspeed']=$rs[$i]['speed'];
							if($arprt[$k]['minspeed']>$rs[$i]['speed'])$arprt[$k]['minspeed']=$rs[$i]['speed'];
							$arprt[$k]['lat']=$rs[$i]['lat'];$arprt[$k]['tm']=$rs[$i]['utimestamp'];
							$arprt[$k]['lng']=$rs[$i]['lng'];
						}
						if(($rs[$i+1]['TypeAcc']!="acc2 started" && $rs[$i+1]['TypeAcc']!="acc2 continued") && ($rs[$i]['TypeAcc']=="acc2 started" || $rs[$i]['TypeAcc']=="acc2 continued"))
						{
							
							$arprt[$k]['type']="Acc";
							$arprt[$k]['weight']=2;	$arprt[$k]['G']=$rs[$i]['accx'];

							if($arprt[$k]['maxspeed']<$rs[$i]['speed'])$arprt[$k]['maxspeed']=$rs[$i]['speed'];
							if($arprt[$k]['minspeed']>$rs[$i]['speed'])$arprt[$k]['minspeed']=$rs[$i]['speed'];
							$arprt[$k]['lat']=$rs[$i]['lat'];
							$arprt[$k]['lng']=$rs[$i]['lng'];
							$arprt[$k]['prespeed']=$prespeed;
							$arprt[$k]['predir']=$predir;$arprt[$k]['tm']=$rs[$i]['utimestamp'];
							$arprt[$k]['time']=date('j/m/y;h:i:s',$rs[$i]['utimestamp']);
							$k++;											$arprt[$k]['maxspeed']=0;
							$arprt[$k]['minspeed']=1000;	$arprt[$k]['duration']=1;
						}
						else
						{
							$prespeed=$rs[$i]['speed'];
							$predir=$rs[$i]['direction'];
						}
						
					}

					for($i=0;$i<count($rs)-1;$i++)
					{
						if(($rs[$i+1]['TypeAcc']=="acc3 started" || $rs[$i+1]['TypeAcc']=="acc3 continued") && ($rs[$i]['TypeAcc']=="acc3 started" || $rs[$i]['TypeAcc']=="acc3 continued"))
						{
							$arprt[$k]['duration']+=$rs[$i+1]['utimestamp']-$rs[$i]['utimestamp'];
							$arprt[$k]['type']="Acc";
							$arprt[$k]['weight']=3;	$arprt[$k]['G']=$rs[$i]['accx'];

							if($arprt[$k]['maxspeed']<$rs[$i]['speed'])$arprt[$k]['maxspeed']=$rs[$i]['speed'];
							if($arprt[$k]['minspeed']>$rs[$i]['speed'])$arprt[$k]['minspeed']=$rs[$i]['speed'];
							$arprt[$k]['lat']=$rs[$i]['lat'];
							$arprt[$k]['lng']=$rs[$i]['lng'];$arprt[$k]['tm']=$rs[$i]['utimestamp'];
						}
						if(($rs[$i+1]['TypeAcc']!="acc3 started" && $rs[$i+1]['TypeAcc']!="acc3 continued") && ($rs[$i]['TypeAcc']=="acc3 started" || $rs[$i]['TypeAcc']=="acc3 continued"))
						{
	
							$arprt[$k]['type']="Acc";
							$arprt[$k]['weight']=3;	$arprt[$k]['G']=$rs[$i]['accx'];

							if($arprt[$k]['maxspeed']<$rs[$i]['speed'])$arprt[$k]['maxspeed']=$rs[$i]['speed'];
							if($arprt[$k]['minspeed']>$rs[$i]['speed'])$arprt[$k]['minspeed']=$rs[$i]['speed'];
							$arprt[$k]['lat']=$rs[$i]['lat'];
							$arprt[$k]['lng']=$rs[$i]['lng'];
							$arprt[$k]['prespeed']=$prespeed;
							$arprt[$k]['predir']=$predir;$arprt[$k]['tm']=$rs[$i]['utimestamp'];
							$arprt[$k]['time']=date('j/m/y;h:i:s',$rs[$i]['utimestamp']);
							$k++;										$arprt[$k]['maxspeed']=0;
							$arprt[$k]['minspeed']=1000;		$arprt[$k]['duration']=1;
						}
						else
						{
							$prespeed=$rs[$i]['speed'];
							$predir=$rs[$i]['direction'];
						}
						
					}

						for($i=0;$i<count($rs)-1;$i++)
					{
						if(($rs[$i+1]['TypeAcc']=="brake1 started" || $rs[$i+1]['TypeAcc']=="brake1 continued") && ($rs[$i]['TypeAcc']=="brake1 started" || $rs[$i]['TypeAcc']=="brake1 continued"))
						{
							$arprt[$k]['duration']+=$rs[$i+1]['utimestamp']-$rs[$i]['utimestamp'];
							$arprt[$k]['type']="Brake";
							$arprt[$k]['weight']=1;	$arprt[$k]['G']=$rs[$i]['accx'];

							if($arprt[$k]['maxspeed']<$rs[$i]['speed'])$arprt[$k]['maxspeed']=$rs[$i]['speed'];
							if($arprt[$k]['minspeed']>$rs[$i]['speed'])$arprt[$k]['minspeed']=$rs[$i]['speed'];
							$arprt[$k]['lat']=$rs[$i]['lat'];$arprt[$k]['tm']=$rs[$i]['utimestamp'];
							$arprt[$k]['lng']=$rs[$i]['lng'];
						}
						if(($rs[$i+1]['TypeAcc']!="brake1 started" && $rs[$i+1]['TypeAcc']!="brake1 continued") && ($rs[$i]['TypeAcc']=="brake1 started" || $rs[$i]['TypeAcc']=="brake1 continued"))
						{
						
							$arprt[$k]['type']="Brake";
							$arprt[$k]['weight']=1;	$arprt[$k]['G']=$rs[$i]['accx'];

							if($arprt[$k]['maxspeed']<$rs[$i]['speed'])$arprt[$k]['maxspeed']=$rs[$i]['speed'];
							if($arprt[$k]['minspeed']>$rs[$i]['speed'])$arprt[$k]['minspeed']=$rs[$i]['speed'];
							$arprt[$k]['lat']=$rs[$i]['lat'];
							$arprt[$k]['lng']=$rs[$i]['lng'];
							$arprt[$k]['prespeed']=$prespeed;
							$arprt[$k]['predir']=$predir;$arprt[$k]['tm']=$rs[$i]['utimestamp'];
							$arprt[$k]['time']=date('j/m/y;h:i:s',$rs[$i]['utimestamp']);
							$k++;										$arprt[$k]['maxspeed']=0;
							$arprt[$k]['minspeed']=1000;		$arprt[$k]['duration']=1;
						}
						else
						{
							$prespeed=$rs[$i]['speed'];
							$predir=$rs[$i]['direction'];
						}
						
					}
	
	
							for($i=0;$i<count($rs)-1;$i++)
					{
						if(($rs[$i+1]['TypeAcc']=="brake2 started" || $rs[$i+1]['TypeAcc']=="brake2 continued") && ($rs[$i]['TypeAcc']=="brake2 started" || $rs[$i]['TypeAcc']=="brake2 continued"))
						{
							$arprt[$k]['duration']+=$rs[$i+1]['utimestamp']-$rs[$i]['utimestamp'];
							$arprt[$k]['type']="Brake";
							$arprt[$k]['weight']=2;	$arprt[$k]['G']=$rs[$i]['accx'];

							if($arprt[$k]['maxspeed']<$rs[$i]['speed'])$arprt[$k]['maxspeed']=$rs[$i]['speed'];
							if($arprt[$k]['minspeed']>$rs[$i]['speed'])$arprt[$k]['minspeed']=$rs[$i]['speed'];
							$arprt[$k]['lat']=$rs[$i]['lat'];$arprt[$k]['tm']=$rs[$i]['utimestamp'];
							$arprt[$k]['lng']=$rs[$i]['lng'];
						}
						if(($rs[$i+1]['TypeAcc']!="brake2 started" && $rs[$i+1]['TypeAcc']!="brake2 continued") && ($rs[$i]['TypeAcc']=="brake2 started" || $rs[$i]['TypeAcc']=="brake2 continued"))
						{
							
							$arprt[$k]['type']="Brake";
							$arprt[$k]['weight']=2;	$arprt[$k]['G']=$rs[$i]['accx'];

							if($arprt[$k]['maxspeed']<$rs[$i]['speed'])$arprt[$k]['maxspeed']=$rs[$i]['speed'];
							if($arprt[$k]['minspeed']>$rs[$i]['speed'])$arprt[$k]['minspeed']=$rs[$i]['speed'];
							$arprt[$k]['lat']=$rs[$i]['lat'];
							$arprt[$k]['lng']=$rs[$i]['lng'];
							$arprt[$k]['prespeed']=$prespeed;
							$arprt[$k]['predir']=$predir;$arprt[$k]['tm']=$rs[$i]['utimestamp'];
							$arprt[$k]['time']=date('j/m/y;h:i:s',$rs[$i]['utimestamp']);
							$k++;											$arprt[$k]['maxspeed']=0;
							$arprt[$k]['minspeed']=1000;	$arprt[$k]['duration']=1;
						}
						else
						{
							$prespeed=$rs[$i]['speed'];
							$predir=$rs[$i]['direction'];
						}
						
					}
	
					for($i=0;$i<count($rs)-1;$i++)
					{
						if(($rs[$i+1]['TypeAcc']=="brake3 started" || $rs[$i+1]['TypeAcc']=="brake3 continued") && ($rs[$i]['TypeAcc']=="brake3 started" || $rs[$i]['TypeAcc']=="brake3 continued"))
						{
							$arprt[$k]['duration']+=$rs[$i+1]['utimestamp']-$rs[$i]['utimestamp'];
							$arprt[$k]['type']="Brake";
							$arprt[$k]['weight']=3;	$arprt[$k]['G']=$rs[$i]['accx'];

							if($arprt[$k]['maxspeed']<$rs[$i]['speed'])$arprt[$k]['maxspeed']=$rs[$i]['speed'];
							if($arprt[$k]['minspeed']>$rs[$i]['speed'])$arprt[$k]['minspeed']=$rs[$i]['speed'];
							$arprt[$k]['lat']=$rs[$i]['lat'];
							$arprt[$k]['lng']=$rs[$i]['lng'];$arprt[$k]['tm']=$rs[$i]['utimestamp'];
						}
						if(($rs[$i+1]['TypeAcc']!="brake3 started" && $rs[$i+1]['TypeAcc']!="brake3 continued") && ($rs[$i]['TypeAcc']=="brake3 started" || $rs[$i]['TypeAcc']=="brake3 continued"))
						{
							
							$arprt[$k]['type']="Brake";
							$arprt[$k]['weight']=3;	$arprt[$k]['G']=$rs[$i]['accx'];

							if($arprt[$k]['maxspeed']<$rs[$i]['speed'])$arprt[$k]['maxspeed']=$rs[$i]['speed'];
							if($arprt[$k]['minspeed']>$rs[$i]['speed'])$arprt[$k]['minspeed']=$rs[$i]['speed'];
							$arprt[$k]['lat']=$rs[$i]['lat'];
							$arprt[$k]['lng']=$rs[$i]['lng'];
							$arprt[$k]['prespeed']=$prespeed;$arprt[$k]['tm']=$rs[$i]['utimestamp'];
							$arprt[$k]['predir']=$predir;
							$arprt[$k]['time']=date('j/m/y;h:i:s',$rs[$i]['utimestamp']);
							$k++;											$arprt[$k]['maxspeed']=0;
							$arprt[$k]['minspeed']=1000;	$arprt[$k]['duration']=1;
						}
						else
						{
							$prespeed=$rs[$i]['speed'];
							$predir=$rs[$i]['direction'];
						}
						
					}

					for($i=0;$i<count($rs)-1;$i++)
					{
						if((($rs[$i+1]['TurnType']=="left turn started" || $rs[$i+1]['TurnType']=="left turn continued"  || $rs[$i+1]['TurnType']=="left turn finished") && $rs[$i+1]['sevTurn']==3) && (($rs[$i]['TurnType']=="left turn started" || $rs[$i]['TurnType']=="left turn continued"  || $rs[$i]['TurnType']=="left turn finished")  && $rs[$i]['sevTurn']==3))
						{
							$arprt[$k]['duration']+=$rs[$i+1]['utimestamp']-$rs[$i]['utimestamp'];
							$arprt[$k]['type']="LeftTurn";
							$arprt[$k]['weight']=3;	$arprt[$k]['G']=$rs[$i]['accx'];

							if($arprt[$k]['maxspeed']<$rs[$i]['speed'])$arprt[$k]['maxspeed']=$rs[$i]['speed'];
							if($arprt[$k]['minspeed']>$rs[$i]['speed'])$arprt[$k]['minspeed']=$rs[$i]['speed'];
							$arprt[$k]['lat']=$rs[$i]['lat'];$arprt[$k]['tm']=$rs[$i]['utimestamp'];
							$arprt[$k]['lng']=$rs[$i]['lng'];
						}
						if(($rs[$i+1]['TurnType']!="left turn started" && $rs[$i+1]['TurnType']!="left turn continued"  && $rs[$i+1]['TurnType']!="left turn finished") && (($rs[$i]['TurnType']=="left turn started" || $rs[$i]['TurnType']=="left turn continued"  || $rs[$i]['TurnType']=="left turn finished") && $rs[$i]['sevTurn']==3))
						{
							
							$arprt[$k]['type']="LeftTurn";
							$arprt[$k]['weight']=3;	$arprt[$k]['G']=$rs[$i]['accx'];

							if($arprt[$k]['maxspeed']<$rs[$i]['speed'])$arprt[$k]['maxspeed']=$rs[$i]['speed'];
							if($arprt[$k]['minspeed']>$rs[$i]['speed'])$arprt[$k]['minspeed']=$rs[$i]['speed'];
							$arprt[$k]['lat']=$rs[$i]['lat'];
							$arprt[$k]['lng']=$rs[$i]['lng'];
							$arprt[$k]['prespeed']=$prespeed;
							$arprt[$k]['predir']=$predir;$arprt[$k]['tm']=$rs[$i]['utimestamp'];
							$arprt[$k]['time']=date('j/m/y;h:i:s',$rs[$i]['utimestamp']);
							$k++;											$arprt[$k]['maxspeed']=0;
							$arprt[$k]['minspeed']=1000;	$arprt[$k]['duration']=1;
						}
						else
						{
							$prespeed=$rs[$i]['speed'];
							$predir=$rs[$i]['direction'];
						}
						
						
					}

					for($i=0;$i<count($rs)-1;$i++)
					{
						if((($rs[$i+1]['TurnType']=="left turn started" || $rs[$i+1]['TurnType']=="left turn continued"  || $rs[$i+1]['TurnType']=="left turn finished") && $rs[$i+1]['sevTurn']==2) && (($rs[$i]['TurnType']=="left turn started" || $rs[$i]['TurnType']=="left turn continued"  || $rs[$i]['TurnType']=="left turn finished")  && $rs[$i]['sevTurn']==2))
						{
							$arprt[$k]['duration']+=$rs[$i+1]['utimestamp']-$rs[$i]['utimestamp'];
							$arprt[$k]['type']="LeftTurn";
							$arprt[$k]['weight']=2;	$arprt[$k]['G']=$rs[$i]['accx'];

							if($arprt[$k]['maxspeed']<$rs[$i]['speed'])$arprt[$k]['maxspeed']=$rs[$i]['speed'];
							if($arprt[$k]['minspeed']>$rs[$i]['speed'])$arprt[$k]['minspeed']=$rs[$i]['speed'];
							$arprt[$k]['lat']=$rs[$i]['lat'];$arprt[$k]['tm']=$rs[$i]['utimestamp'];
							$arprt[$k]['lng']=$rs[$i]['lng'];
						}
						if(($rs[$i+1]['TurnType']!="left turn started" && $rs[$i+1]['TurnType']!="left turn continued"  && $rs[$i+1]['TurnType']!="left turn finished") && (($rs[$i]['TurnType']=="left turn started" || $rs[$i]['TurnType']=="left turn continued"  || $rs[$i]['TurnType']=="left turn finished") && $rs[$i]['sevTurn']==2))
						{
							
							$arprt[$k]['type']="LeftTurn";
							$arprt[$k]['weight']=2;	$arprt[$k]['G']=$rs[$i]['accx'];

							if($arprt[$k]['maxspeed']<$rs[$i]['speed'])$arprt[$k]['maxspeed']=$rs[$i]['speed'];
							if($arprt[$k]['minspeed']>$rs[$i]['speed'])$arprt[$k]['minspeed']=$rs[$i]['speed'];
							$arprt[$k]['lat']=$rs[$i]['lat'];
							$arprt[$k]['lng']=$rs[$i]['lng'];
							$arprt[$k]['prespeed']=$prespeed;
							$arprt[$k]['predir']=$predir;$arprt[$k]['tm']=$rs[$i]['utimestamp'];
							$arprt[$k]['time']=date('j/m/y;h:i:s',$rs[$i]['utimestamp']);
							$k++;											$arprt[$k]['maxspeed']=0;
							$arprt[$k]['minspeed']=1000;	$arprt[$k]['duration']=1;
						}
						else
						{
							$prespeed=$rs[$i]['speed'];
							$predir=$rs[$i]['direction'];
						}
						
						
					}

					for($i=0;$i<count($rs)-1;$i++)
					{
						if((($rs[$i+1]['TurnType']=="left turn started" || $rs[$i+1]['TurnType']=="left turn continued"  || $rs[$i+1]['TurnType']=="left turn finished") && $rs[$i+1]['sevTurn']==1) && (($rs[$i]['TurnType']=="left turn started" || $rs[$i]['TurnType']=="left turn continued"  || $rs[$i]['TurnType']=="left turn finished")  && $rs[$i]['sevTurn']==1))
						{
							$arprt[$k]['duration']+=$rs[$i+1]['utimestamp']-$rs[$i]['utimestamp'];
							$arprt[$k]['type']="LeftTurn";
							$arprt[$k]['weight']=1;	$arprt[$k]['G']=$rs[$i]['accx'];

							if($arprt[$k]['maxspeed']<$rs[$i]['speed'])$arprt[$k]['maxspeed']=$rs[$i]['speed'];
							if($arprt[$k]['minspeed']>$rs[$i]['speed'])$arprt[$k]['minspeed']=$rs[$i]['speed'];
							$arprt[$k]['lat']=$rs[$i]['lat'];$arprt[$k]['tm']=$rs[$i]['utimestamp'];
							$arprt[$k]['lng']=$rs[$i]['lng'];
						}
						if(($rs[$i+1]['TurnType']!="left turn started" && $rs[$i+1]['TurnType']!="left turn continued"  && $rs[$i+1]['TurnType']!="left turn finished") && (($rs[$i]['TurnType']=="left turn started" || $rs[$i]['TurnType']=="left turn continued"  || $rs[$i]['TurnType']=="left turn finished") && $rs[$i]['sevTurn']==1))
						{
							
							$arprt[$k]['type']="LeftTurn";
							$arprt[$k]['weight']=1;	$arprt[$k]['G']=$rs[$i]['accx'];

							if($arprt[$k]['maxspeed']<$rs[$i]['speed'])$arprt[$k]['maxspeed']=$rs[$i]['speed'];
							if($arprt[$k]['minspeed']>$rs[$i]['speed'])$arprt[$k]['minspeed']=$rs[$i]['speed'];
							$arprt[$k]['lat']=$rs[$i]['lat'];
							$arprt[$k]['lng']=$rs[$i]['lng'];
							$arprt[$k]['prespeed']=$prespeed;
							$arprt[$k]['predir']=$predir;$arprt[$k]['tm']=$rs[$i]['utimestamp'];
							$arprt[$k]['time']=date('j/m/y;h:i:s',$rs[$i]['utimestamp']);
							$k++;											$arprt[$k]['maxspeed']=0;
							$arprt[$k]['minspeed']=1000;	$arprt[$k]['duration']=1;
						}
						else
						{
							$prespeed=$rs[$i]['speed'];
							$predir=$rs[$i]['direction'];
						}
						
						
					}


					for($i=0;$i<count($rs)-1;$i++)
					{
						if((($rs[$i+1]['TurnType']=="right turn started" || $rs[$i+1]['TurnType']=="right turn continued"  || $rs[$i+1]['TurnType']=="right turn finished") && $rs[$i+1]['sevTurn']==3) && (($rs[$i]['TurnType']=="right turn started" || $rs[$i]['TurnType']=="right turn continued"  || $rs[$i]['TurnType']=="right turn finished")  && $rs[$i]['sevTurn']==3))
						{
							$arprt[$k]['duration']+=$rs[$i+1]['utimestamp']-$rs[$i]['utimestamp'];
							$arprt[$k]['type']="RightTurn";
							$arprt[$k]['weight']=3;	$arprt[$k]['G']=$rs[$i]['accx'];

							if($arprt[$k]['maxspeed']<$rs[$i]['speed'])$arprt[$k]['maxspeed']=$rs[$i]['speed'];
							if($arprt[$k]['minspeed']>$rs[$i]['speed'])$arprt[$k]['minspeed']=$rs[$i]['speed'];
							$arprt[$k]['lat']=$rs[$i]['lat'];$arprt[$k]['tm']=$rs[$i]['utimestamp'];
							$arprt[$k]['lng']=$rs[$i]['lng'];
						}
						if(($rs[$i+1]['TurnType']!="right turn started" && $rs[$i+1]['TurnType']!="right turn continued"  && $rs[$i+1]['TurnType']!="right turn finished") && (($rs[$i]['TurnType']=="right turn started" || $rs[$i]['TurnType']=="right turn continued"  || $rs[$i]['TurnType']=="right turn finished") && $rs[$i]['sevTurn']==3))
						{
							
							$arprt[$k]['type']="RightTurn";
							$arprt[$k]['weight']=3;	$arprt[$k]['G']=$rs[$i]['accx'];

							if($arprt[$k]['maxspeed']<$rs[$i]['speed'])$arprt[$k]['maxspeed']=$rs[$i]['speed'];
							if($arprt[$k]['minspeed']>$rs[$i]['speed'])$arprt[$k]['minspeed']=$rs[$i]['speed'];
							$arprt[$k]['lat']=$rs[$i]['lat'];
							$arprt[$k]['lng']=$rs[$i]['lng'];
							$arprt[$k]['prespeed']=$prespeed;
							$arprt[$k]['predir']=$predir;$arprt[$k]['tm']=$rs[$i]['utimestamp'];
							$arprt[$k]['time']=date('j/m/y;h:i:s',$rs[$i]['utimestamp']);
							$k++;											$arprt[$k]['maxspeed']=0;
							$arprt[$k]['minspeed']=1000;	$arprt[$k]['duration']=1;
						}
						else
						{
							$prespeed=$rs[$i]['speed'];
							$predir=$rs[$i]['direction'];
						}
						
						
					}

					for($i=0;$i<count($rs)-1;$i++)
					{
						if((($rs[$i+1]['TurnType']=="right turn started" || $rs[$i+1]['TurnType']=="right turn continued"  || $rs[$i+1]['TurnType']=="right turn finished") && $rs[$i+1]['sevTurn']==2) && (($rs[$i]['TurnType']=="right turn started" || $rs[$i]['TurnType']=="right turn continued"  || $rs[$i]['TurnType']=="right turn finished")  && $rs[$i]['sevTurn']==2))
						{
							$arprt[$k]['duration']+=$rs[$i+1]['utimestamp']-$rs[$i]['utimestamp'];
							$arprt[$k]['type']="RightTurn";
							$arprt[$k]['weight']=2;	$arprt[$k]['G']=$rs[$i]['accx'];

							if($arprt[$k]['maxspeed']<$rs[$i]['speed'])$arprt[$k]['maxspeed']=$rs[$i]['speed'];
							if($arprt[$k]['minspeed']>$rs[$i]['speed'])$arprt[$k]['minspeed']=$rs[$i]['speed'];
							$arprt[$k]['lat']=$rs[$i]['lat'];$arprt[$k]['tm']=$rs[$i]['utimestamp'];
							$arprt[$k]['lng']=$rs[$i]['lng'];
						}
						if(($rs[$i+1]['TurnType']!="right turn started" && $rs[$i+1]['TurnType']!="right turn continued"  && $rs[$i+1]['TurnType']!="right turn finished") && (($rs[$i]['TurnType']=="right turn started" || $rs[$i]['TurnType']=="right turn continued"  || $rs[$i]['TurnType']=="right turn finished") && $rs[$i]['sevTurn']==2))
						{
							
							$arprt[$k]['type']="RightTurn";
							$arprt[$k]['weight']=2;	$arprt[$k]['G']=$rs[$i]['accx'];

							if($arprt[$k]['maxspeed']<$rs[$i]['speed'])$arprt[$k]['maxspeed']=$rs[$i]['speed'];
							if($arprt[$k]['minspeed']>$rs[$i]['speed'])$arprt[$k]['minspeed']=$rs[$i]['speed'];
							$arprt[$k]['lat']=$rs[$i]['lat'];
							$arprt[$k]['lng']=$rs[$i]['lng'];
							$arprt[$k]['prespeed']=$prespeed;
							$arprt[$k]['predir']=$predir;$arprt[$k]['tm']=$rs[$i]['utimestamp'];
							$arprt[$k]['time']=date('j/m/y;h:i:s',$rs[$i]['utimestamp']);
							$k++;											$arprt[$k]['maxspeed']=0;
							$arprt[$k]['minspeed']=1000;	$arprt[$k]['duration']=1;
						}
						else
						{
							$prespeed=$rs[$i]['speed'];
							$predir=$rs[$i]['direction'];
						}
						
						
					}

					for($i=0;$i<count($rs)-1;$i++)
					{
						if((($rs[$i+1]['TurnType']=="right turn started" || $rs[$i+1]['TurnType']=="right turn continued"  || $rs[$i+1]['TurnType']=="right turn finished") && $rs[$i+1]['sevTurn']==1) && (($rs[$i]['TurnType']=="right turn started" || $rs[$i]['TurnType']=="right turn continued"  || $rs[$i]['TurnType']=="right turn finished")  && $rs[$i]['sevTurn']==1))
						{
							$arprt[$k]['duration']+=$rs[$i+1]['utimestamp']-$rs[$i]['utimestamp'];
							$arprt[$k]['type']="RightTurn";
							$arprt[$k]['weight']=1;	$arprt[$k]['G']=$rs[$i]['accx'];

							if($arprt[$k]['maxspeed']<$rs[$i]['speed'])$arprt[$k]['maxspeed']=$rs[$i]['speed'];
							if($arprt[$k]['minspeed']>$rs[$i]['speed'])$arprt[$k]['minspeed']=$rs[$i]['speed'];
							$arprt[$k]['lat']=$rs[$i]['lat'];$arprt[$k]['tm']=$rs[$i]['utimestamp'];
							$arprt[$k]['lng']=$rs[$i]['lng'];
						}
						if(($rs[$i+1]['TurnType']!="right turn started" && $rs[$i+1]['TurnType']!="right turn continued"  && $rs[$i+1]['TurnType']!="right turn finished") && (($rs[$i]['TurnType']=="right turn started" || $rs[$i]['TurnType']=="right turn continued"  || $rs[$i]['TurnType']=="right turn finished") && $rs[$i]['sevTurn']==1))
						{
							
							$arprt[$k]['type']="RightTurn";
							$arprt[$k]['weight']=1;	$arprt[$k]['G']=$rs[$i]['accx'];

							if($arprt[$k]['maxspeed']<$rs[$i]['speed'])$arprt[$k]['maxspeed']=$rs[$i]['speed'];
							if($arprt[$k]['minspeed']>$rs[$i]['speed'])$arprt[$k]['minspeed']=$rs[$i]['speed'];
							$arprt[$k]['lat']=$rs[$i]['lat'];
							$arprt[$k]['lng']=$rs[$i]['lng'];
							$arprt[$k]['prespeed']=$prespeed;
							$arprt[$k]['predir']=$predir;$arprt[$k]['tm']=$rs[$i]['utimestamp'];
							$arprt[$k]['time']=date('j/m/y;h:i:s',$rs[$i]['utimestamp']);
							$k++;											$arprt[$k]['maxspeed']=0;
							$arprt[$k]['minspeed']=1000;	$arprt[$k]['duration']=1;
						}
						else
						{
							$prespeed=$rs[$i]['speed'];
							$predir=$rs[$i]['direction'];
						}
						
						
					}

					for($i=0;$i<count($rs)-1;$i++)
					{
						if(($rs[$i+1]['TypeSpeed']=="s1") && ($rs[$i]['TypeSpeed']=="s1"))
						{
							$arprt[$k]['duration']+=$rs[$i+1]['utimestamp']-$rs[$i]['utimestamp'];
							$arprt[$k]['type']="Speed";
							$arprt[$k]['weight']=1;	$arprt[$k]['G']=$rs[$i]['accx'];

							if($arprt[$k]['maxspeed']<$rs[$i]['speed'])$arprt[$k]['maxspeed']=$rs[$i]['speed'];
							if($arprt[$k]['minspeed']>$rs[$i]['speed'])$arprt[$k]['minspeed']=$rs[$i]['speed'];
							$arprt[$k]['lat']=$rs[$i]['lat'];
							$arprt[$k]['lng']=$rs[$i]['lng'];$arprt[$k]['tm']=$rs[$i]['utimestamp'];
						}
						if(($rs[$i+1]['TypeSpeed']!="s1") && ($rs[$i]['TypeSpeed']=="s1"))
						{
							
							$arprt[$k]['type']="Speed";
							$arprt[$k]['weight']=1;	$arprt[$k]['G']=$rs[$i]['accx'];

							if($arprt[$k]['maxspeed']<$rs[$i]['speed'])$arprt[$k]['maxspeed']=$rs[$i]['speed'];
							if($arprt[$k]['minspeed']>$rs[$i]['speed'])$arprt[$k]['minspeed']=$rs[$i]['speed'];
							$arprt[$k]['lat']=$rs[$i]['lat'];
							$arprt[$k]['lng']=$rs[$i]['lng'];
							$arprt[$k]['prespeed']=$prespeed;
							$arprt[$k]['predir']=$predir;$arprt[$k]['tm']=$rs[$i]['utimestamp'];
							$arprt[$k]['time']=date('j/m/y;h:i:s',$rs[$i]['utimestamp']);
							$k++;											$arprt[$k]['maxspeed']=0;
							$arprt[$k]['minspeed']=1000;	$arprt[$k]['duration']=1;
						}
						else
						{
							$prespeed=$rs[$i]['speed'];
							$predir=$rs[$i]['direction'];
						}
						
						
					}


							for($i=0;$i<count($rs)-1;$i++)
					{
						if(($rs[$i+1]['TypeSpeed']=="s2") && ($rs[$i]['TypeSpeed']=="s2"))
						{
							$arprt[$k]['duration']+=$rs[$i+1]['utimestamp']-$rs[$i]['utimestamp'];
							$arprt[$k]['type']="Speed";	$arprt[$k]['G']=$rs[$i]['accx'];

							$arprt[$k]['weight']=2;
							if($arprt[$k]['maxspeed']<$rs[$i]['speed'])$arprt[$k]['maxspeed']=$rs[$i]['speed'];
							if($arprt[$k]['minspeed']>$rs[$i]['speed'])$arprt[$k]['minspeed']=$rs[$i]['speed'];
							$arprt[$k]['lat']=$rs[$i]['lat'];$arprt[$k]['tm']=$rs[$i]['utimestamp'];
							$arprt[$k]['lng']=$rs[$i]['lng'];
						}
						if(($rs[$i+1]['TypeSpeed']!="s2") && ($rs[$i]['TypeSpeed']=="s2"))
						{
							
							$arprt[$k]['type']="Speed";
							$arprt[$k]['weight']=2;	$arprt[$k]['G']=$rs[$i]['accx'];

							if($arprt[$k]['maxspeed']<$rs[$i]['speed'])$arprt[$k]['maxspeed']=$rs[$i]['speed'];
							if($arprt[$k]['minspeed']>$rs[$i]['speed'])$arprt[$k]['minspeed']=$rs[$i]['speed'];
							$arprt[$k]['lat']=$rs[$i]['lat'];
							$arprt[$k]['lng']=$rs[$i]['lng'];
							$arprt[$k]['prespeed']=$prespeed;
							$arprt[$k]['predir']=$predir;$arprt[$k]['tm']=$rs[$i]['utimestamp'];
							$arprt[$k]['time']=date('j/m/y;h:i:s',$rs[$i]['utimestamp']);
							$k++;											$arprt[$k]['maxspeed']=0;
							$arprt[$k]['minspeed']=1000;	$arprt[$k]['duration']=1;
						}
						else
						{
							$prespeed=$rs[$i]['speed'];
							$predir=$rs[$i]['direction'];
						}
						
						
					}
							for($i=0;$i<count($rs)-1;$i++)
					{
						if(($rs[$i+1]['TypeSpeed']=="s3") && ($rs[$i]['TypeSpeed']=="s3"))
						{
							$arprt[$k]['duration']+=$rs[$i+1]['utimestamp']-$rs[$i]['utimestamp'];
							$arprt[$k]['type']="Speed";	$arprt[$k]['G']=$rs[$i]['accx'];

							$arprt[$k]['weight']=3;
							if($arprt[$k]['maxspeed']<$rs[$i]['speed'])$arprt[$k]['maxspeed']=$rs[$i]['speed'];
							if($arprt[$k]['minspeed']>$rs[$i]['speed'])$arprt[$k]['minspeed']=$rs[$i]['speed'];
							$arprt[$k]['lat']=$rs[$i]['lat'];
							$arprt[$k]['lng']=$rs[$i]['lng'];$arprt[$k]['tm']=$rs[$i]['utimestamp'];
						}
						if(($rs[$i+1]['TypeSpeed']!="s3") && ($rs[$i]['TypeSpeed']=="s3"))
						{
							
							$arprt[$k]['type']="Speed";
							$arprt[$k]['weight']=3;	$arprt[$k]['G']=$rs[$i]['accx'];

							if($arprt[$k]['maxspeed']<$rs[$i]['speed'])$arprt[$k]['maxspeed']=$rs[$i]['speed'];
							if($arprt[$k]['minspeed']>$rs[$i]['speed'])$arprt[$k]['minspeed']=$rs[$i]['speed'];
							$arprt[$k]['lat']=$rs[$i]['lat'];
							$arprt[$k]['lng']=$rs[$i]['lng'];
							$arprt[$k]['prespeed']=$prespeed;
							$arprt[$k]['tm']=$rs[$i]['utimestamp'];
							$arprt[$k]['predir']=$predir;
							$arprt[$k]['time']=date('j/m/y;h:i:s',$rs[$i]['utimestamp']);
							$k++;											$arprt[$k]['maxspeed']=0;
							$arprt[$k]['minspeed']=1000;	$arprt[$k]['duration']=1;
						}
						else
						{
							$prespeed=$rs[$i]['speed'];
							$predir=$rs[$i]['direction'];
						}
					}
					
					
					for($i=0;$i<count($rs)-1;$i++)
					{
							$arprt[$k]['type']="Normal";
							$arprt[$k]['weight']=0;
							$arprt[$k]['maxspeed']=$rs[$i]['speed'];
							$arprt[$k]['minspeed']=$rs[$i]['speed'];
							$arprt[$k]['speed']=$rs[$i]['speed'];
							$arprt[$k]['lat']=$rs[$i]['lat'];
							$arprt[$k]['lng']=$rs[$i]['lng'];
							$arprt[$k]['tm']=$rs[$i]['utimestamp'];
								$arprt[$k]['G']=$rs[$i]['accx'];
							$arprt[$k]['duration']=1;
							$arprt[$k]['time']=date('j/m/y;h:i:s',$rs[$i]['utimestamp']);
							$k++;											
					}

					for($i=0;$i<count($arprt);$i++)
					for($j=0;$j<count($arprt)-1;$j++)
					{
						if( $arprt[$j]['tm']> $arprt[$j+1]['tm'])
						{
							$tp=$arprt[$j];
							$arprt[$j]=$arprt[$j+1];
							$arprt[$j+1]=$tp;
						}
					}
				for($i=0;$i<count($arprt);$i++)
				{
						echo $arprt[$i]['lat'].",".$arprt[$i]['lng'].",".(($arprt[$i]['minspeed']+$arprt[$i]['maxspeed'])/2).",".$arprt[$i]['time'].",".$arprt[$i]['type'].",".$arprt[$i]['weight'].",".$arprt[$i]['G'].",".$arprt[$i]['duration']."\n";
				}

	 }
	 else
	 {
		 header("Location: http://nti.goodroads.ru/");
	 }
}
	
	


}
