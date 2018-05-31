<?php
class User_mod_test extends TestCase {

    private $ci_obj;

    public function setUp() {
        $this->resetInstance();
        $this->ci_obj = $this->CI;
        $this->ci_obj->load->model('admin/user_mod');
    }

    public function test_add_user() {
        $this->assertEquals(1,1);
    }

}
