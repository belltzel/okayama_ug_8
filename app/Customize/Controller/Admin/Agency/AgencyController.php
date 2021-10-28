<?php

namespace Customize\Controller\Admin\Agency;

use Customize\Entity\Agency;
use Customize\Form\Type\Admin\AgencyType;
use Customize\Form\Type\Admin\SearchAgencyType;
use Customize\Repository\AgencyRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Eccube\Controller\AbstractController;
use Eccube\Repository\Master\PageMaxRepository;
use Eccube\Util\FormUtil;
use Knp\Component\Pager\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class AgencyController extends AbstractController
{

    /**
     * @var PageMaxRepository
     */
    protected $pageMaxRepository;

    /**
     * @var AgencyRepository
     */
    protected $agencyRepository;

    public function __construct(
        PageMaxRepository $pageMaxRepository,
        AgencyRepository $agencyRepository
    ) {
        $this->pageMaxRepository = $pageMaxRepository;
        $this->agencyRepository = $agencyRepository;
    }

    /**
     * @Route("/%eccube_admin_route%/agency", name="customize_admin_agency")
     * @Route("/%eccube_admin_route%/agency/page/{page_no}", requirements={"page_no" = "\d+"}, name="customize_admin_agency_page")
     * @Template("@admin/Agency/index.twig")
     */
    public function index(Request $request, $page_no = null, Paginator $paginator)
    {
        $session = $this->session;
        $builder = $this->formFactory->createBuilder(SearchAgencyType::class);
        $searchForm = $builder->getForm();

        $pageMaxis = $this->pageMaxRepository->findAll();
        $pageCount = $session->get('eccube.customize.admin.agency.search.page_count', $this->eccubeConfig['eccube_default_page_count']);
        $pageCountParam = $request->get('page_count');
        if ($pageCountParam && is_numeric($pageCountParam)) {
            foreach ($pageMaxis as $pageMax) {
                if ($pageCountParam == $pageMax->getName()) {
                    $pageCount = $pageMax->getName();
                    $session->set('eccube.customize.admin.agency.search.page_count', $pageCount);
                    break;
                }
            }
        }

        if ('POST' === $request->getMethod()) {
            $searchForm->handleRequest($request);
            if ($searchForm->isValid()) {
                $searchData = $searchForm->getData();
                $page_no = 1;

                $session->set('eccube.customize.admin.agency.search', FormUtil::getViewData($searchForm));
                $session->set('eccube.customize.admin.agency.search.page_no', $page_no);
            } else {
                return [
                    'searchForm' => $searchForm->createView(),
                    'pagination' => [],
                    'pageMaxis' => $pageMaxis,
                    'page_no' => $page_no,
                    'page_count' => $pageCount,
                    'has_errors' => true,
                ];
            }
        } else {
            if (null !== $page_no || $request->get('resume')) {
                if ($page_no) {
                    $session->set('eccube.customize.admin.agency.search.page_no', (int) $page_no);
                } else {
                    $page_no = $session->get('eccube.customize.admin.agency.search.page_no', 1);
                }
                $viewData = $session->get('eccube.customize.admin.agency.search', []);
            } else {
                $page_no = 1;
                $viewData = FormUtil::getViewData($searchForm);
                $session->set('eccube.customize.admin.agency.search', $viewData);
                $session->set('eccube.customize.admin.agency.search.page_no', $page_no);
            }
            $searchData = FormUtil::submitAndGetData($searchForm, $viewData);
        }

        /** @var QueryBuilder $qb */
        $qb = $this->agencyRepository->getQueryBuilderBySearchData($searchData);

        $pagination = $paginator->paginate(
            $qb,
            $page_no,
            $pageCount
        );

        return [
            'searchForm' => $searchForm->createView(),
            'pagination' => $pagination,
            'pageMaxis' => $pageMaxis,
            'page_no' => $page_no,
            'page_count' => $pageCount,
            'has_errors' => false,
        ];
    }

    /**
     * @Route("/%eccube_admin_route%/agency/new", name="customize_admin_agency_new")
     * @Route("/%eccube_admin_route%/agency/{id}/edit", requirements={"id" = "\d+"}, name="customize_admin_agency_edit")
     * @Template("@admin/Agency/edit.twig")
     */
    public function edit(Request $request, $id = null)
    {

        if ($id) {
            // 編集
            $Agency = $this->agencyRepository->find($id);

            if (is_null($Agency)) {
                throw new NotFoundHttpException();
            }

        } else {
            // 新規登録
            $Agency = $this->agencyRepository->newAgency();
        }

        // 代理店フォーム
        $builder = $this->formFactory
            ->createBuilder(AgencyType::class, $Agency);

        $form = $builder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            log_info('代理店登録開始', [$Agency->getId()]);

            $this->agencyRepository->save($Agency);

            log_info('代理店登録完了', [$Agency->getId()]);

            $this->addSuccess('admin.common.save_complete', 'admin');

            return $this->redirectToRoute('customize_admin_agency_edit', ['id' => $Agency->getId()]);
        }

        return [
            'form' => $form->createView(),
            'Agency' => $Agency,
        ];
    }

    /**
     * @Route("/%eccube_admin_route%/agency/{id}/delete", requirements={"id" = "\d+"}, name="customize_admin_agency_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Agency $Agency)
    {
        $this->isTokenValid();

        log_info('代理店削除開始', [$Agency->getId()]);

        try {
            $this->agencyRepository->delete($Agency);

            $this->addSuccess('admin.common.delete_complete', 'admin');

            log_info('代理店削除完了', [$Agency->getId()]);
        } catch (ForeignKeyConstraintViolationException $e) {
            log_info('代理店削除エラー', [$Agency->getId()]);

            $message = trans('admin.common.delete_error_foreign_key', ['%name%' => $Agency->getName()]);
            $this->addError($message, 'admin');
        } catch (\Exception $e) {
            log_info('代理店削除エラー', [$Agency->getId(), $e]);

            $message = trans('admin.common.delete_error');
            $this->addError($message, 'admin');
        }

        return $this->redirectToRoute('customize_admin_agency');
    }
}
