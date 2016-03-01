<?php

class CandidateImg {
    private $alt,$filename;
    private $img_dir,$img_dir_url;
    
    public function __construct($candidate_id,$candidate_slug,$alt = '') {
        $this->filename = "$candidate_id-$candidate_slug.jpg";
        $this->alt = htmlentities($alt);
        if($GLOBALS['config']['cpanel']){
            $this->img_dir = "/home/".$GLOBALS['config']['user']."/user_".$GLOBALS['config']['user']."/candidate-images";
        }else{
            $this->img_dir = "/var/www/".$GLOBALS['config']['dir']."/user_".$GLOBALS['config']['user']."/candidate-images";
        }
        $this->img_dir_url = "//".$GLOBALS['config']['front_domain']."/candidate-images";        
    }    
    public function front_end($placeholder = null) {
        if(is_file("$this->img_dir/$this->filename")){?>
            <img class="candidate-thumb img-thumbnail media-object" src="
            <?php echo "$this->img_dir_url/$this->filename";
            ?>" alt="<?=$this->alt?>" /><?php
        }else{
            if($placeholder===null){
                ?><div class="no-candidate-img-placeholder"></div><?php
            }else{ ?>
                <img class="candidate-thumb img-thumbnail media-object" src="
                <?php echo "$this->img_dir_url/$placeholder";
                ?>" alt="<?=$this->alt?>" /><?php
            }
        }
    }
}
