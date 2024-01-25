<style>
/*progress bar*/
.m-categry-heading {
  display: flex;
  align-items: center;
  width: 100%;
}

.m-text h2,
.m-text p {
  margin: 0;
}

.active-category {
  background: url(https://partners.rankmf.com/images/partners_dashboard/sprite-category-icons.png) no-repeat;
  width: 56px;
  height: 56px;
  margin-right: 10px;
  animation-duration: 0.5s;
}

.active-core {
  background-position: -2px -1px;
}

.active-core-plus {
  background-position: -56px -1px;
}

.active-prime {
  background-position: -110px -1px;
}

.active-prime-plus {
  background-position: -166px -1px;
}

.active-elite {
  background-position: -221px -1px;
}

.active-elite-plus {
  background-position: -276px -1px;
}

.m-category-progress {
  display: flex;
  align-items: center;
  margin-top: 30px;
  position: relative;
  z-index: 1;
}

.m--category {
  width: 22.6666666666%;
}

.m-category-progress .m--category:last-child {
  width: 37px;
}

.m-category-progress .m--category:first-child {
  margin-left: -15px;
}

.progress-icons {
  width: 39px;
  height: 39px;
  background: url(https://partners.rankmf.com/images/partners_dashboard/sprite-category-icons.png) no-repeat;
  position: relative;
}

.core {
  background-position: -2px -66px;
}

.core-plus {
  background-position: -58px -66px;
}

.prime {
  background-position: -116px -66px;
}

.prime-plus {
  background-position: -173px -66px;
}

.elite {
  background-position: -230px -66px;
}

.elite-plus {
  background-position: -287px -66px;
}

.prime.active {
  width: 50px;
  height: 50px;
  border: solid 1px #63cb2c;
  border-radius: 50%;
  background-position: -111px -62px;
}

.prime-plus.active {
  width: 50px;
  height: 50px;
  border: solid 1px #f75671;
  border-radius: 50%;
  background-position: -168px -62px;
}

.core.active {
  width: 50px;
  height: 50px;
  border: solid 1px #c1c1c1;
  border-radius: 50%;
  background-position: 3px -62px;
}

.elite.active {
  width: 50px;
  height: 50px;
  border: solid 1px #00c7de;
  border-radius: 50%;
  background-position: -225px -61px;
}

.core-plus.active {
  width: 50px;
  height: 50px;
  border: solid 1px #e3b12d;
  border-radius: 50%;
  background-position: -54px -62px;
}

.elite-plus.active {
  width: 50px;
  height: 50px;
  border: solid 1px #00c7de;
  border-radius: 50%;
  background-position: -282px -61px;
}

.core-plus.inactive {
  background-position: -58px -116px;
}

.prime.inactive {
  background-position: -116px -118px;
}

.prime-plus.inactive {
  background-position: -173px -116px;
}

.elite.inactive {
  background-position: -230px -116px;
}

.elite-plus.inactive {
  background-position: -287px -116px;
}

.m-category-progress:before {
  content: "";
  border: solid;
  position: absolute;
  width: calc(100% - 8px);
  z-index: -1;
  margin: 0 4px;
  border-color: #eaeaea;
}

/* .m-category-progress:after {
  content: "";
  border: solid;
  position: absolute;
  width: calc(10% - 8px);
  z-index: -1;
  margin: 0 4px;
  border-color: #e3b12d;
} */

.pro-txt {
  position: absolute;
  bottom: -20px;
  font-size: 13px;
  font-weight: 600;
  width: 79px;
  right: -17px;
  text-align: center;
}

.pro-hover-tip {
  position: absolute;

  width: 52px;
  text-align: center;
  background: #3a80e0;
  border-radius: 30px;
  font-size: 12px;
  line-height: 22px;
  box-shadow: 0px 1px 4px 0 rgba(58, 128, 224, 0.52);
  color: #ffffff;
  font-weight: 400;
  left: -4px;
  bottom: 0;
  top: unset;
  z-index: -100;
  opacity: 0;
}

.datepicker.datepicker-orient-top {
  z-index: 9999 !important;
}

.pro-hover-tip:after {
  content: "";
  border: solid;
  border-color: #3a80e0 rgba(0, 128, 0, 0) rgba(255, 255, 0, 0) rgba(0, 0, 255, 0);
  border-width: 5px;
  position: absolute;
  bottom: -10px;
  left: 0;
  right: 0;
  margin: auto;
  width: 10px;
}

.progress-icons:hover .pro-hover-tip {
  bottom: unset;
  top: -32px;
  z-index: 1;
  opacity: 1;
}

@media (max-width: 480px) {
  .m-categry-heading {
    display: block;
    text-align: center;
  }

  .active-category {
    margin: 0 auto;
    margin-bottom: 20px;
  }

  .pro-txt {
    display: none;
  }

  .progress-icons.active {
    display: none;
  }

  .progress-icons {
    background-image: none;
  }
}


.share-mob {
  display: none !important;
}

.share-desk {
  display: block;
}

@media (max-width: 768px) {
  .share-desk {
    display: none !important;
  }

  .share-mob {
    display: table-cell !important;
  }

  .m-header__bottom,
  .m-container--responsive {
    clear: both !important;
  }

  .core-plus.active {
    display: none;
  }
}
</style>
<?php $category_array = array('', 'CORE', 'CORE PLUS', 'PRIME', 'PRIME PLUS', 'ELITE', 'ELITE PLUS');
$category_class = array('', 'core', 'core-plus', 'prime', 'prime-plus', 'elite', 'elite-plus');
if ($progress_bar['status'] == 'success' && !empty($progress_bar['data'])) {
    $progress_arr       = ['core' => 1, 'core-plus' => 2, 'prime' => 3, 'prime-plus' => 4, 'elite' => 5, 'elite-plus' => 6];
    $progress_per_arr = ['core' => 20, 'core-plus' => 40, 'prime' => 60, 'prime-plus' => 80, 'elite' => 100, 'elite-plus' => 100];
    $active_class = str_replace('_', '-', $category_class[$progress_bar['data'][0]->progress]);
    $percent_prog_temp = (($progress_bar['aum'] - $progress_bar['data'][0]->min) * 100) / ($progress_bar['data'][0]->max - $progress_bar['data'][0]->min);
    $active_classes = $progress_arr[$active_class];
    $min   = $progress_per_arr[$active_class] - 20;
    $percent_prog        = (($percent_prog_temp * 20) / 100) + $min;
?>
    <style>
        .m-category-progress:after {
            content: "";
            border: solid;
            position: absolute;
            /* width: calc(100% - 8px); */
            z-index: -1;
            margin: 0 4px;
            border-color: #e3b12d;
        }
    </style>
    <div class="row">
        <div class="col-xl-12">
            <div class="m-portlet  m-portlet--full-height">
                <div class="m-portlet__body">
                    <div class="m-categry-heading">
                        <div class="active-category active-{{$category_class[$progress_bar['data'][0]->progress]}}"></div>
                        <div class="m-text">
                            <h2><?php
                                echo str_replace('_', ' ', strtoupper($category_array[$progress_bar['data'][0]->progress])); ?></h2>
                            <?php if (($category != 6)) { ?>
                                <p>You are Rs <span class="m--font-brand"> <?php echo $progress_bar['all_tiers'][$progress_bar['data'][0]->progress]->min - $progress_bar['aum']; ?></span> Cr of AUM away from upgrading your partner level</p>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="m-category-progress">
                        <?php foreach ($progress_arr as $k => $v) {
                            //x($progress_arr);
                            $progress_class = '';
                            if ($v == $active_classes) {
                                $progress_class = ' active';
                            } elseif ($v < $active_classes) {
                                $progress_class = '';
                            } elseif ($v > $active_classes) {
                                $progress_class = ' inactive';
                            }
                        ?>
                            <div class="m--category">
                                <div class="progress-icons <?php echo $k . $progress_class; ?>">
                                    <div class="pro-txt"><?php echo str_replace('-', ' ', strtoupper($k)); ?></div>
                                    <div class="pro-hover-tip">
                                        <?php foreach ($progress_bar['all_tiers'] as $key => $tier) {
                                            if (str_replace('-', '_', $k) != 'elite_plus') {
                                                if ($tier->progress == str_replace('-', '_', $k)) {
                                                    echo $tier->min . ' Cr';
                                                }
                                            } else {
                                                if ($key == 5)
                                                    echo '80Cr+ ';
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>