<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchRequest;
use App\Mail\ContactMail;
use App\Repositories\PageRepository;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\ContactMail as ContactRequest;


class PageController extends BaseController
{
    public function __construct(PageRepository $repository)
    {
        parent::__construct();
        $this->repository = $repository;
    }


    public function index($slug = "/", string $locale = null)
    {

       $result = parent::index($slug, $locale);

       if(is_object($result)) return $result;

       $data = ['data' => $this->data];

        if(!empty($this->data->custom_fields)) {
            $custom_fields = json_decode($this->data->custom_fields, true);
            $data['custom_fields'] = $custom_fields;
        }

       return view('default.pages.'.$this->data->template, $data);
    }


    public function sendMail(ContactRequest $request)
    {
        $data = array(
            'first_name'      =>  $request->first_name,
            'last_name'      =>  $request->last_name,
            'subject'      =>  $request->subject,
            'email'      =>  $request->email,
            'message'   =>   $request->message
        );

        $contact_mail = get_contact_email();

        if(!$contact_mail) $contact_mail = config('mail.contact_address');

        Mail::to($contact_mail)->send(new ContactMail($data));


        return back()->with('success', \Lang::get('default/page.contact_message_success'));
    }


    public function search()
    {
        return view('default.pages.search');
    }

    public function searchResult(SearchRequest $request, $page = 1, $count=10)
    {
        $searchData['query'] = $request->get('query');
        $searchData['type'] = $request->get('filter');
        $result = $this->repository->getSearchResult($request, $page, $count);
        $searchData['result'] = $result;

        return view('default.pages.search', compact('searchData'));
    }



    public function paginatedResult($string, $filter, $page)
    {
        $searchData['query'] = $string;
        $searchData['type'] = $filter;
        $result = $this->repository->getPaginatedSearchResult($string, $filter, $page);
        $searchData['result'] = $result;

        return view('default.pages.search', compact('searchData'));
    }




}
