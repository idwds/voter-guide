<?php
class Helper {
    private static $initialized = false;
    public static $helpers;
    
    public $message,$id,$dismissed,$title,$nextLink;
    private function __construct($id,$title,$nextLink,$message) {
	$this->message = $message;
	$this->id = $id;
	$this->title = $title;
	$this->nextLink = $nextLink;
	$this->dismissed = false;
    }
    public static function init(){
	self::$helpers = array();
	self::$initialized = true;
	if(Session::exists('helpers')){
	    self::restoreHelpers();
	}
    }
    private static function restoreHelpers(){
	self::$helpers = unserialize(Session::get('helpers'));	
    }
    /**
     * Stores the curent state
     * @uses Session Session class
     */
    private static function saveInSession(){
	Session::put('helpers', serialize(self::$helpers));
    }
    public static function reset(){
	Session::delete('helpers');
	self::$helpers = array();
    }
    public static function addHelper($id,$title,$nextLink,$message){
	if(!isset(self::$helpers[$id])){
	    self::$helpers[$id] = new Helper($id,$title,$nextLink,$message);
	    self::saveInSession();
	    return array('id'=>$id,'title'=>$title,'nextLink'=>$nextLink,'message'=>$message);
	}return null;
    }
    public static function dismissHelper($id){
	self::$helpers[$id]->dismissed = true;
	self::saveInSession();
    }
    public static function getActiveHelpers(){
	$out = array();
	foreach(self::$helpers as $helper){
	    if(!$helper->dismissed){
		$out[] = $helper;
	    }
	}
	return $out;
    }
    private static function hasActiveHelpers(){	
	foreach(self::$helpers as $helper){
	    if(!$helper->dismissed){
		return true;
	    }
	}
	return false;
    }
    private static function activeHelperCount(){
	$count = 0;
	foreach(self::$helpers as $helper){
	    if(!$helper->dismissed){
		$count++;
	    }
	}
	return $count;
    }
    public static function outputModalHTML(){
	$helper = isset(Helper::getActiveHelpers()[0])? Helper::getActiveHelpers()[0] : new Helper('', '', '', '');
	?>
	<!-- Modal -->
	<div class="modal fade" id="helperModal" data-backdrop="false" data-helper-id="<?php echo $helper->id ?>" tabindex="-1" role="dialog" aria-labelledby="helperModalLabel" aria-hidden="true">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
		<h4 class="modal-title" id="helperModalLabel"><?php echo $helper->title ?></h4>
	      </div>
	      <div class="modal-body">
		<?php echo $helper->message ?>
	      </div>
	      <div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		<a id="modal-next" href="<?php echo $helper->nextLink ?>" class="btn btn-primary">Next</a>
	      </div>
	    </div>
	  </div>
	</div>	
	    <?php
    }
    public static function outputMainScript(){
	?><script>
	    function dismissModal(){
		var helperId = $('#helperModal').data('helper-id');
		//alert('Hiding'+helperId);
		$.getJSON( "/do-ajax",{
		    action:'helper-dismiss',
		    id:helperId,
		    token:'<?php echo Session::$token ?>'
		});
	    }
	    function updateModal(ob){
		/*
		 *  id: "twoCandidatesRace-666"
		    message: "You've selected two, now compare them by clicking 'Compare these two' or 'Next' below."
		    nextLink: "/compare-candidates?race=666&token=tj4k3udp8892gqj8er801k6ah0"
		    title: "Compare"
		/*/
		$('#helperModal').data('helper-id',ob.id);
		$('#helperModalLabel').html(ob.title);
		$('.modal-body').html(ob.message);
		$('#modal-next').attr('href',ob.nextLink);
		jQuery('#helperModal').modal('show');
		$("html, body").animate({ scrollTop: 0 }, "slow");
		
		
	    }
	    //jQuery('#helperModal').modal({backdrop:false});
	    $('#helperModal').on('shown.bs.modal', function () {dismissModal()});
	    $('#helperModal').on('show.bs.modal', function () {
		var href = $('#modal-next').attr('href');
		if(href==''){
		    $('#modal-next').hide();
		}else{
		    $('#modal-next').show();
		}
		scrollToTop();
		
	    });
	    <?php
	if(self::hasActiveHelpers()){
	    $activeHelperCount = self::activeHelperCount();
	    if($activeHelperCount>1){
		?>		
		var activeHelperCount = <?php echo $activeHelperCount ?>;
		$('#helperModal').on('hidden.bs.modal', function (e) {
		    activeHelperCount--;
		    if(activeHelperCount>0){
			jQuery('#helperModal').modal('show');
		    }
		});		
		<?php
	    }
	    ?>
	    jQuery('#helperModal').modal('show')
		<?php
	}
	?>

	    console.log(<?php echo json_encode(self::$helpers) ?>);
	</script>
	<?php
    }
}