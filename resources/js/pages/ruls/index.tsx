import AppLayout from '@/layouts/app-layout';
import { index, create, destroy } from '@/routes/ruls';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Sheet,
    SheetContent,
    SheetDescription,
    SheetHeader,
    SheetTitle,
} from '@/components/ui/sheet';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import {
    Tabs,
    TabsContent,
    TabsList,
    TabsTrigger,
} from '@/components/ui/tabs';
import { Edit, Trash2, Plus, Eye, FileText } from 'lucide-react';
import { useState } from 'react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Resource Unit Leaders',
        href: index().url,
    },
];

interface Certificate {
    uuid: string;
    certificate_name: string;
    file_path: string;
}

interface Rul {
    uuid: string;
    name: string;
    contact_number: string;
    serial_number: string;
    department: string;
    signature?: string;
    certificates: Certificate[];
    created_at: string;
}

interface PaginatedRuls {
    data: Rul[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

interface IndexProps {
    ruls: PaginatedRuls;
}

export default function Index({ ruls }: IndexProps) {
    const [selectedRul, setSelectedRul] = useState<Rul | null>(null);
    const [isViewSheetOpen, setIsViewSheetOpen] = useState(false);
    const [deleteDialogOpen, setDeleteDialogOpen] = useState(false);
    const [rulToDelete, setRulToDelete] = useState<string | null>(null);

    const handleView = (rul: Rul) => {
        setSelectedRul(rul);
        setIsViewSheetOpen(true);
    };

    const handleDeleteClick = (uuid: string) => {
        setRulToDelete(uuid);
        setDeleteDialogOpen(true);
    };

    const confirmDelete = () => {
        if (rulToDelete) {
            router.delete(destroy({ rul: rulToDelete }).url);
            setDeleteDialogOpen(false);
            setRulToDelete(null);
        }
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Resource Unit Leaders" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <Card>
                    <CardHeader>
                        <div className="flex items-center justify-between">
                            <div>
                                <CardTitle>Resource Unit Leaders</CardTitle>
                                <CardDescription>
                                    Manage resource unit leaders and their certificates
                                </CardDescription>
                            </div>
                            <Link href={create().url}>
                                <Button>
                                    <Plus className="mr-2 h-4 w-4" />
                                    Create New
                                </Button>
                            </Link>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div className="rounded-md border">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Name</TableHead>
                                        <TableHead>Serial Number</TableHead>
                                        <TableHead>Department</TableHead>
                                        <TableHead>Contact Number</TableHead>
                                        <TableHead>Certificates</TableHead>
                                        <TableHead>Signature</TableHead>
                                        <TableHead className="text-right">Actions</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {ruls.data.length === 0 ? (
                                        <TableRow>
                                            <TableCell
                                                colSpan={7}
                                                className="h-24 text-center"
                                            >
                                                No resource unit leaders found.
                                            </TableCell>
                                        </TableRow>
                                    ) : (
                                        ruls.data.map((rul) => (
                                            <TableRow key={rul.uuid}>
                                                <TableCell className="font-medium">
                                                    {rul.name}
                                                </TableCell>
                                                <TableCell>{rul.serial_number}</TableCell>
                                                <TableCell>{rul.department}</TableCell>
                                                <TableCell>{rul.contact_number}</TableCell>
                                                <TableCell>
                                                    {rul.certificates.length} certificate(s)
                                                </TableCell>
                                                <TableCell>
                                                    {rul.signature ? (
                                                        <span className="text-green-600">✓ Signed</span>
                                                    ) : (
                                                        <span className="text-gray-400">No signature</span>
                                                    )}
                                                </TableCell>
                                                <TableCell className="text-right">
                                                    <div className="flex justify-end gap-2">
                                                        <Button
                                                            variant="outline"
                                                            size="sm"
                                                            onClick={() => handleView(rul)}
                                                        >
                                                            <Eye className="h-4 w-4" />
                                                        </Button>
                                                        <Link
                                                            href={`/ruls/${rul.uuid}/edit`}
                                                        >
                                                            <Button
                                                                variant="outline"
                                                                size="sm"
                                                            >
                                                                <Edit className="h-4 w-4" />
                                                            </Button>
                                                        </Link>
                                                        <Button
                                                            variant="destructive"
                                                            size="sm"
                                                            onClick={() =>
                                                                handleDeleteClick(rul.uuid)
                                                            }
                                                        >
                                                            <Trash2 className="h-4 w-4" />
                                                        </Button>
                                                    </div>
                                                </TableCell>
                                            </TableRow>
                                        ))
                                    )}
                                </TableBody>
                            </Table>
                        </div>
                        {ruls.last_page > 1 && (
                            <div className="mt-4 flex items-center justify-between">
                                <div className="text-sm text-muted-foreground">
                                    Showing {(ruls.current_page - 1) * ruls.per_page + 1} to{' '}
                                    {Math.min(ruls.current_page * ruls.per_page, ruls.total)} of{' '}
                                    {ruls.total} results
                                </div>
                                <div className="flex gap-2">
                                    {ruls.current_page > 1 && (
                                        <Link
                                            href={index({ query: { page: ruls.current_page - 1 } }).url}
                                        >
                                            <Button variant="outline">Previous</Button>
                                        </Link>
                                    )}
                                    {ruls.current_page < ruls.last_page && (
                                        <Link
                                            href={index({ query: { page: ruls.current_page + 1 } }).url}
                                        >
                                            <Button variant="outline">Next</Button>
                                        </Link>
                                    )}
                                </div>
                            </div>
                        )}
                    </CardContent>
                </Card>
            </div>

            {/* View Sheet */}
            <Sheet open={isViewSheetOpen} onOpenChange={setIsViewSheetOpen}>
                <SheetContent className="w-[500px] sm:w-[640px] overflow-y-auto">
                    {selectedRul && (
                        <>
                            <SheetHeader>
                                <SheetTitle>Resource Unit Leader Details</SheetTitle>
                                <SheetDescription>
                                    Detailed information about the selected resource unit leader.
                                </SheetDescription>
                            </SheetHeader>
                            <Tabs defaultValue="basic" className="p-4">
                                <TabsList className="grid w-full grid-cols-2">
                                    <TabsTrigger value="basic">Basic Information</TabsTrigger>
                                    <TabsTrigger value="certificates">
                                        Certificates ({selectedRul.certificates.length})
                                    </TabsTrigger>
                                </TabsList>
                                <TabsContent value="basic" className="space-y-4 mt-2">
                                    <div>
                                        <label className="text-sm font-medium text-gray-500">
                                            Name
                                        </label>
                                        <p className="mt-1 text-base">{selectedRul.name}</p>
                                    </div>
                                    <div>
                                        <label className="text-sm font-medium text-gray-500">
                                            Serial Number
                                        </label>
                                        <p className="mt-1 text-base">
                                            {selectedRul.serial_number}
                                        </p>
                                    </div>
                                    <div>
                                        <label className="text-sm font-medium text-gray-500">
                                            Contact Number
                                        </label>
                                        <p className="mt-1 text-base">
                                            {selectedRul.contact_number}
                                        </p>
                                    </div>
                                    <div>
                                        <label className="text-sm font-medium text-gray-500">
                                            Department
                                        </label>
                                        <p className="mt-1 text-base">
                                            {selectedRul.department}
                                        </p>
                                    </div>
                                    <div>
                                        <label className="text-sm font-medium text-gray-500">
                                            Signature
                                        </label>
                                        {selectedRul.signature ? (
                                            <div className="mt-2 border rounded-md p-4 bg-white">
                                                <img
                                                    src={`/storage/${selectedRul.signature}`}
                                                    alt="Signature"
                                                    className="max-h-32 mx-auto"
                                                />
                                            </div>
                                        ) : (
                                            <p className="mt-1 text-sm text-gray-400">
                                                No signature available
                                            </p>
                                        )}
                                    </div>
                                </TabsContent>
                                <TabsContent value="certificates" className="space-y-4 mt-2">
                                    {selectedRul.certificates.length === 0 ? (
                                        <p className="text-sm text-gray-400 text-center py-8">
                                            No certificates uploaded
                                        </p>
                                    ) : (
                                        <div className="space-y-3">
                                            {selectedRul.certificates.map((cert) => (
                                                <div
                                                    key={cert.uuid}
                                                    className="border rounded-lg p-4 hover:bg-gray-50 transition-colors"
                                                >
                                                    <div className="flex items-start gap-3">
                                                        <FileText className="h-5 w-5 text-blue-600 mt-0.5 flex-shrink-0" />
                                                        <div className="flex-1 min-w-0">
                                                            <p className="font-medium text-sm break-words">
                                                                {cert.certificate_name}
                                                            </p>
                                                            <a
                                                                href={`/storage/${cert.file_path}`}
                                                                target="_blank"
                                                                rel="noopener noreferrer"
                                                                className="text-xs text-blue-600 hover:underline mt-1 inline-block"
                                                            >
                                                                View Certificate
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            ))}
                                        </div>
                                    )}
                                </TabsContent>
                            </Tabs>
                        </>
                    )}
                </SheetContent>
            </Sheet>

            {/* Delete Confirmation Dialog */}
            <Dialog open={deleteDialogOpen} onOpenChange={setDeleteDialogOpen}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Are you absolutely sure?</DialogTitle>
                        <DialogDescription>
                            This action cannot be undone. This will permanently delete the
                            Resource Unit Leader and all associated certificates.
                        </DialogDescription>
                    </DialogHeader>
                    <DialogFooter>
                        <Button
                            variant="outline"
                            onClick={() => setDeleteDialogOpen(false)}
                        >
                            Cancel
                        </Button>
                        <Button variant="destructive" onClick={confirmDelete}>
                            Delete
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </AppLayout>
    );
}
